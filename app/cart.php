<?php
declare(strict_types=1);
function positive_int(mixed $value,int $max=PHP_INT_MAX): ?int {
    if(!is_int($value)&&!is_string($value))return null;
    if(!preg_match('/^[1-9][0-9]{0,18}$/D',(string)$value))return null;
    if(strlen((string)$value)>strlen((string)$max)||(strlen((string)$value)===strlen((string)$max)&&strcmp((string)$value,(string)$max)>0))return null;
    return (int)$value;
}
function price_int(mixed $value): ?int {return $value===0||$value==='0'?0:positive_int($value);}
function catalog_products(): array {return fetch_json(getenv('CATALOG_URL').'/products')['products'];}
function cart_view(int $buyer): array {
    $pdo=db();$q=$pdo->prepare("SELECT id,version FROM carts WHERE buyer_id=? AND status='active'");$q->execute([$buyer]);$cart=$q->fetch(PDO::FETCH_ASSOC);
    if(!$cart)return ['cart_id'=>null,'cart_version'=>null,'items'=>[],'total'=>0,'currency'=>'IDR_DUMMY'];
    $q=$pdo->prepare('SELECT product_id,quantity FROM cart_items WHERE cart_id=? ORDER BY product_id');$q->execute([$cart['id']]);
    $catalog=array_column(catalog_products(),null,'id');$items=[];$total=0;
    foreach($q->fetchAll(PDO::FETCH_ASSOC) as $item){
        $product=$catalog[$item['product_id']]??null;
        if(!$product)web_error('INVALID_INPUT',400);
        $quantity=(int)$item['quantity'];$price=price_int($product['price']);
        if($price===null||$price>intdiv(PHP_INT_MAX,$quantity)||$total>PHP_INT_MAX-$price*$quantity)web_error('INVALID_INPUT',400);
        $line=$price*$quantity;$total+=$line;
        $items[]=['product_id'=>$product['id'],'name'=>$product['name'],'price'=>$price,'quantity'=>$quantity,'total'=>$line];
    }
    return ['cart_id'=>(int)$cart['id'],'cart_version'=>(int)$cart['version'],'items'=>$items,'total'=>$total,'currency'=>'IDR_DUMMY'];
}
function mutate_cart(int $buyer,array $data,string $operation): void {
    if(!is_string($data['product_id']??null)||!preg_match('/^[A-Za-z0-9_-]{1,64}$/D',$data['product_id']))web_error('INVALID_INPUT',400);
    $quantity=$operation==='remove'?1:positive_int($data['quantity']??null,20);if($quantity===null)web_error('INVALID_INPUT',400);
    $products=array_column(catalog_products(),null,'id');$product=$products[$data['product_id']]??null;
    if(!$product||price_int($product['price'])===null)web_error('INVALID_INPUT',400);
    $pdo=db();$pdo->beginTransaction();
    try{
        $q=$pdo->prepare('SELECT id FROM buyers WHERE id=? AND active FOR UPDATE');$q->execute([$buyer]);
        $q=$pdo->prepare("SELECT id FROM carts WHERE buyer_id=? AND status='active' FOR UPDATE");$q->execute([$buyer]);$id=$q->fetchColumn();
        if(!$id){$q=$pdo->prepare('INSERT INTO carts(buyer_id) VALUES(?) RETURNING id');$q->execute([$buyer]);$id=$q->fetchColumn();}
        if($operation==='remove'){$q=$pdo->prepare('DELETE FROM cart_items WHERE cart_id=? AND product_id=?');$q->execute([$id,$data['product_id']]);}
        else {
            $q=$pdo->prepare('SELECT count(*) FROM cart_items WHERE cart_id=?');$q->execute([$id]);$count=(int)$q->fetchColumn();
            $q=$pdo->prepare('SELECT quantity FROM cart_items WHERE cart_id=? AND product_id=?');$q->execute([$id,$data['product_id']]);$old=$q->fetchColumn();
            $new=$operation==='add'?$quantity+(int)$old:$quantity;
            if($new>20||($old===false&&$count>=50)){$pdo->rollBack();web_error('INVALID_INPUT',400);}
            $q=$pdo->prepare('SELECT product_id,quantity FROM cart_items WHERE cart_id=?');$q->execute([$id]);$candidate=$q->fetchAll(PDO::FETCH_KEY_PAIR);$candidate[$data['product_id']]=$new;$total=0;
            foreach($candidate as $productId=>$candidateQuantity){
                $candidateProduct=$products[$productId]??null;$price=$candidateProduct?price_int($candidateProduct['price']):null;$candidateQuantity=(int)$candidateQuantity;
                if($price===null||$candidateProduct['currency']!=='IDR_DUMMY'||$price>intdiv(PHP_INT_MAX,$candidateQuantity)||$total>PHP_INT_MAX-$price*$candidateQuantity){$pdo->rollBack();web_error('INVALID_INPUT',400);}
                $total+=$price*$candidateQuantity;
            }
            $q=$pdo->prepare('INSERT INTO cart_items(cart_id,product_id,quantity) VALUES(?,?,?) ON CONFLICT(cart_id,product_id) DO UPDATE SET quantity=excluded.quantity');$q->execute([$id,$data['product_id'],$new]);
        }
        $q=$pdo->prepare('UPDATE carts SET version=version+1 WHERE id=?');$q->execute([$id]);$pdo->commit();
    }catch(Throwable $error){if($pdo->inTransaction())$pdo->rollBack();throw $error;}
}
