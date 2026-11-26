<?php
declare(strict_types=1);
class BusinessProblem extends RuntimeException {
    public function __construct(public readonly string $problem,public readonly int $status){parent::__construct($problem);}
}
function checkout_order(array $data,mixed $key): array {
    $buyer=positive_int($data['buyer_id']??null);$cart=positive_int($data['cart_id']??null);$version=positive_int($data['cart_version']??null);
    if(!$buyer||!$cart||!$version||!is_string($key)||!preg_match('/^[A-Za-z0-9_-]{1,128}$/D',$key))throw new BusinessProblem('INVALID_INPUT',400);
    $pdo=db();$pdo->beginTransaction();
    try{
        $q=$pdo->prepare('SELECT id FROM buyers WHERE id=? AND active FOR UPDATE');$q->execute([$buyer]);if(!$q->fetchColumn())throw new BusinessProblem('NOT_FOUND',404);
        $q=$pdo->prepare('SELECT fingerprint,result FROM checkout_requests WHERE buyer_id=? AND checkout_key=?');$q->execute([$buyer,$key]);$previous=$q->fetch(PDO::FETCH_ASSOC);
        if($previous){
            if($previous['fingerprint']!==$cart.':'.$version)throw new BusinessProblem('CONFLICT',409);
            $result=json_decode($previous['result'],true,512,JSON_THROW_ON_ERROR);$pdo->commit();return $result;
        }
        $q=$pdo->prepare('SELECT id,version,status FROM carts WHERE id=? AND buyer_id=? FOR UPDATE');$q->execute([$cart,$buyer]);$row=$q->fetch(PDO::FETCH_ASSOC);
        if(!$row)throw new BusinessProblem('NOT_FOUND',404);
        if($row['status']!=='active'||(int)$row['version']!==$version)throw new BusinessProblem('CONFLICT',409);
        $q=$pdo->prepare('SELECT p.id,p.name,p.price,p.currency,p.active,ci.quantity FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.cart_id=? ORDER BY p.id FOR SHARE OF p');$q->execute([$cart]);$items=$q->fetchAll(PDO::FETCH_ASSOC);
        if(!$items)throw new BusinessProblem('EMPTY_CART',400);
        if(count($items)>50)throw new BusinessProblem('INVALID_INPUT',400);
        $total=0;
        foreach($items as &$item){
            $price=price_int($item['price']);$quantity=positive_int($item['quantity'],20);
            if(!$item['active']||$item['currency']!=='IDR_DUMMY'||$price===null||$quantity===null||$price>intdiv(PHP_INT_MAX,$quantity)||$total>PHP_INT_MAX-$price*$quantity)throw new BusinessProblem('INVALID_INPUT',400);
            $item['line_total']=$price*$quantity;$total+=$item['line_total'];
        }unset($item);
        $q=$pdo->prepare('INSERT INTO orders(buyer_id,cart_id,total) VALUES(?,?,?) RETURNING id');$q->execute([$buyer,$cart,$total]);$order=(int)$q->fetchColumn();
        $q=$pdo->prepare('INSERT INTO order_items(order_id,product_id,name,price,quantity,total) VALUES(?,?,?,?,?,?)');foreach($items as $item)$q->execute([$order,$item['id'],$item['name'],$item['price'],$item['quantity'],$item['line_total']]);
        $q=$pdo->prepare('INSERT INTO jobs(order_id) VALUES(?)');$q->execute([$order]);
        $result=['order_id'=>$order,'total'=>$total,'currency'=>'IDR_DUMMY'];
        $q=$pdo->prepare('INSERT INTO checkout_requests(buyer_id,checkout_key,cart_id,cart_version,fingerprint,order_id,result) VALUES(?,?,?,?,?,?,?)');$q->execute([$buyer,$key,$cart,$version,$cart.':'.$version,$order,json_encode($result,JSON_THROW_ON_ERROR)]);
        $q=$pdo->prepare("UPDATE carts SET status='consumed' WHERE id=?");$q->execute([$cart]);$pdo->commit();return $result;
    }catch(Throwable $error){if($pdo->inTransaction())$pdo->rollBack();throw $error;}
}
function send_checkout(array $payload,string $key): array {
    $options=['http'=>['method'=>'POST','timeout'=>5,'ignore_errors'=>true,'header'=>"Content-Type: application/json\r\nAccept: application/json\r\nAuthorization: Bearer ".secret('CHECKOUT_TOKEN')."\r\nCheckout-Key: ".$key."\r\n",'content'=>json_encode($payload,JSON_THROW_ON_ERROR)]];
    $body=@file_get_contents(getenv('CHECKOUT_URL').'/internal/orders',false,stream_context_create($options));
    if($body===false)throw new RuntimeException('DEPENDENCY_UNAVAILABLE');
    $status=503;if(preg_match('~^HTTP/\S+ (\d+)~',$http_response_header[0]??'',$match))$status=(int)$match[1];
    $data=json_decode($body,true,512,JSON_THROW_ON_ERROR);
    if($status>=400)throw new BusinessProblem($data['error']??'DEPENDENCY_UNAVAILABLE',$status);
    return $data;
}
function order_view(int $buyer,int $id): array {
    $pdo=db();$q=$pdo->prepare('SELECT id,total,currency,status,created_at,updated_at FROM orders WHERE id=? AND buyer_id=?');$q->execute([$id,$buyer]);$order=$q->fetch(PDO::FETCH_ASSOC);if(!$order)web_error('NOT_FOUND',404);
    $order['id']=(int)$order['id'];$order['total']=(int)$order['total'];
    $q=$pdo->prepare('SELECT product_id,name,price,quantity,total FROM order_items WHERE order_id=? ORDER BY product_id');$q->execute([$id]);$order['items']=$q->fetchAll(PDO::FETCH_ASSOC);
    foreach($order['items'] as &$item){foreach(['price','quantity','total'] as $field)$item[$field]=(int)$item[$field];}
    $q=$pdo->prepare('SELECT state,attempts,error_code FROM jobs WHERE order_id=?');$q->execute([$id]);$job=$q->fetch(PDO::FETCH_ASSOC);
    if($job)$job['attempts']=(int)$job['attempts'];$order['job']=$job;
    return $order;
}
