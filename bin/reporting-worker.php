<?php
declare(strict_types=1);
require __DIR__.'/../app/worker.php';
run_worker('reporting-worker',function(PDO $pdo):array {
    $q=$pdo->prepare("WITH refreshed AS (
      INSERT INTO buyer_reports(buyer_id,completed_count,completed_total,updated_at,generation_id)
      SELECT b.id,count(o.id),coalesce(sum(o.total),0),now(),:generation FROM buyers b
      LEFT JOIN orders o ON o.buyer_id=b.id AND o.status='completed' GROUP BY b.id
      ON CONFLICT(buyer_id) DO UPDATE SET completed_count=excluded.completed_count,completed_total=excluded.completed_total,updated_at=excluded.updated_at,generation_id=excluded.generation_id
      RETURNING completed_count,completed_total
    ) SELECT count(*) AS refreshed,coalesce(sum(completed_count),0) AS completed_orders,coalesce(sum(completed_total),0) AS completed_total FROM refreshed");
    $q->execute(['generation'=>bin2hex(random_bytes(16))]);$result=$q->fetch(PDO::FETCH_ASSOC);
    return array_map(fn($v)=>(int)$v,$result);
},$argv);
