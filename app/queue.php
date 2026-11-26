<?php
declare(strict_types=1);
function queue_batch(PDO $pdo): array {
    $lease=max(5,min(300,(int)(getenv('QUEUE_LEASE_SECONDS')?:30)));
    $result=['examined'=>0,'claimed'=>0,'completed'=>0,'retried'=>0,'failed'=>0,'stale'=>0,'mode'=>'order-processing'];
    $pdo->beginTransaction();$claims=[];
    try{
        $rows=$pdo->query("SELECT id,order_id,attempts FROM jobs WHERE (state='queued' AND available_at<=clock_timestamp()) OR (state='processing' AND lease_until<=clock_timestamp()) ORDER BY id LIMIT 20 FOR UPDATE SKIP LOCKED")->fetchAll(PDO::FETCH_ASSOC);
        $result['examined']=count($rows);
        foreach($rows as $row){
            if((int)$row['attempts']>=3){
                $pdo->prepare("UPDATE jobs SET state='failed',lease_until=NULL,lease_token=NULL,error_code='ATTEMPTS_EXHAUSTED' WHERE id=?")->execute([$row['id']]);
                $pdo->prepare("UPDATE orders SET status='failed',updated_at=clock_timestamp() WHERE id=? AND status<>'completed'")->execute([$row['order_id']]);$result['failed']++;continue;
            }
            $token=bin2hex(random_bytes(24));
            $q=$pdo->prepare("UPDATE jobs SET state='processing',attempts=attempts+1,lease_token=?,lease_until=clock_timestamp()+(? * interval '1 second'),error_code=NULL WHERE id=? RETURNING attempts");$q->execute([$token,$lease,$row['id']]);
            $row['attempts']=(int)$q->fetchColumn();$row['token']=$token;
            $pdo->prepare("UPDATE orders SET status='processing',updated_at=clock_timestamp() WHERE id=? AND status<>'completed'")->execute([$row['order_id']]);$claims[]=$row;
        }
        $pdo->commit();
    }catch(Throwable $error){if($pdo->inTransaction())$pdo->rollBack();throw $error;}
    $result['claimed']=count($claims);
    foreach($claims as $claim){
      try{
        $q=$pdo->prepare('SELECT o.total,o.currency,count(i.order_id) AS item_count,coalesce(sum(i.total),0)::text AS item_total FROM orders o LEFT JOIN order_items i ON i.order_id=o.id WHERE o.id=? GROUP BY o.id');$q->execute([$claim['order_id']]);$order=$q->fetch(PDO::FETCH_ASSOC);
        if(!$order||(int)$order['item_count']===0||(string)$order['total']!==$order['item_total']||$order['currency']!=='IDR_DUMMY')throw new RuntimeException('INVALID_ORDER');
        $pdo->beginTransaction();
        try{
            $q=$pdo->prepare("SELECT id FROM jobs WHERE id=? AND state='processing' AND lease_token=? AND lease_until>clock_timestamp() FOR UPDATE");$q->execute([$claim['id'],$claim['token']]);
            if(!$q->fetchColumn()){$pdo->rollBack();$result['stale']++;continue;}
            $pdo->prepare("UPDATE orders SET status='completed',updated_at=clock_timestamp() WHERE id=? AND status='processing'")->execute([$claim['order_id']]);
            $pdo->prepare("UPDATE jobs SET state='completed',lease_until=NULL,lease_token=NULL,error_code=NULL WHERE id=? AND lease_token=?")->execute([$claim['id'],$claim['token']]);
            $pdo->commit();$result['completed']++;
        }catch(Throwable $error){if($pdo->inTransaction())$pdo->rollBack();throw $error;}
      }catch(Throwable $error){
        if($pdo->inTransaction())$pdo->rollBack();
        $pdo->beginTransaction();
        try{
            $q=$pdo->prepare("SELECT attempts FROM jobs WHERE id=? AND state='processing' AND lease_token=? AND lease_until>clock_timestamp() FOR UPDATE");$q->execute([$claim['id'],$claim['token']]);$attempts=$q->fetchColumn();
            if($attempts===false){$pdo->rollBack();$result['stale']++;continue;}
            $terminal=(int)$attempts>=3;$state=$terminal?'failed':'queued';
            $q=$pdo->prepare("UPDATE jobs SET state=?,lease_until=NULL,lease_token=NULL,available_at=clock_timestamp()+interval '2 seconds',error_code='JOB_PROCESSING_FAILED' WHERE id=? AND lease_token=?");$q->execute([$state,$claim['id'],$claim['token']]);
            $q=$pdo->prepare("UPDATE orders SET status=?,updated_at=clock_timestamp() WHERE id=? AND status='processing'");$q->execute([$state,$claim['order_id']]);
            $pdo->commit();$result[$terminal?'failed':'retried']++;
        }catch(Throwable $failure){if($pdo->inTransaction())$pdo->rollBack();throw $failure;}
      }
    }
    return $result;
}
