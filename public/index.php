<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
$workload=getenv('WORKLOAD_ID') ?: 'storefront';
header('X-Workload-ID: '.$workload);header('X-PHP-Version: '.PHP_VERSION);
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$method=$_SERVER['REQUEST_METHOD'];
try {
 if($path==='/health' && in_array($method,['GET','HEAD'],true))json_response(readiness());
 if($workload==='checkout' && in_array($path,['/internal/readiness','/internal/orders'],true) && $method==='POST') {
  if(!hash_equals('Bearer '.secret('CHECKOUT_TOKEN'),$_SERVER['HTTP_AUTHORIZATION']??''))json_response(['error'=>'UNAUTHORIZED'],401);
  if($path==='/internal/readiness')json_response(readiness());
  require __DIR__.'/../app/web.php';
  try {$result=checkout_order(request_data(),$_SERVER['HTTP_CHECKOUT_KEY']??null);json_response($result,201);}
  catch(BusinessProblem $error){json_response(['error'=>$error->problem],$error->status);}
 }
 if($workload==='storefront'){require __DIR__.'/../app/web.php';storefront($path,$method);}
 if(!in_array($method,['GET','HEAD'],true))json_response(['error'=>'METHOD_NOT_ALLOWED'],405);
 if($workload==='catalog-api') {
  if($path==='/products') {
   $products=db()->query('SELECT id,name,description,price,currency FROM products WHERE active ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
   foreach($products as &$product)$product['price']=(int)$product['price'];
   json_response(['products'=>$products]);
  }
  if(preg_match('~^/products/([A-Za-z0-9_-]+)$~',$path,$m)) {
   $q=db()->prepare('SELECT id,name,description,price,currency FROM products WHERE id=? AND active');$q->execute([$m[1]]);$product=$q->fetch(PDO::FETCH_ASSOC);
   if(!$product)json_response(['error'=>'NOT_FOUND'],404);$product['price']=(int)$product['price'];json_response($product);
  }
 }
 if($workload==='storefront' && $path==='/') {
  $data=fetch_json(getenv('CATALOG_URL').'/products');
  header('Content-Type: text/html; charset=utf-8');
  echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>NusaMart</title><h1>NusaMart</h1><p>Fondasi aplikasi. Pembayaran dummy; alur toko belum tersedia.</p><ul>';
  foreach($data['products'] as $product)echo '<li>'.htmlspecialchars($product['name'],ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').' — '.(int)$product['price'].' IDR_DUMMY</li>';
  echo '</ul></html>';exit;
 }
 json_response(['error'=>'NOT_FOUND'],404);
} catch(Throwable $e) {
 safe_error('http_dependency_failed');
 if($workload==='storefront'&&$path!=='/health'){require_once __DIR__.'/../app/web.php';web_error($e instanceof OutOfBoundsException?'NOT_FOUND':'DEPENDENCY_UNAVAILABLE',$e instanceof OutOfBoundsException?404:503);}
 json_response(['error'=>'DEPENDENCY_UNAVAILABLE'],503);
}
