<?php
declare(strict_types=1);
require __DIR__.'/cart.php';
require_once __DIR__.'/checkout.php';
require_once __DIR__.'/view.php';
function wants_json(): bool {return str_contains($_SERVER['HTTP_ACCEPT']??'', 'application/json');}
function h(mixed $text): string {return htmlspecialchars((string)$text,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function redirect_to(string $path): never {header('Location: '.$path,true,303);exit;}
function csrf_field(): string {return '<input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'">';}
function web_error(string $code,int $status): never {
    if(wants_json())json_response(['error'=>$code],$status);
    $messages=['UNAUTHORIZED'=>'Silakan masuk terlebih dahulu.','CSRF_INVALID'=>'Form kedaluwarsa atau tidak sah. Muat ulang halaman lalu coba lagi.','INVALID_INPUT'=>'Input tidak valid. Periksa produk dan jumlah (1–20).','NOT_FOUND'=>'Halaman atau data tidak ditemukan.','CONFLICT'=>'Keranjang berubah atau permintaan bertentangan. Muat ulang keranjang sebelum mencoba lagi.','EMPTY_CART'=>'Keranjang kosong. Tambahkan produk sebelum checkout.','DEPENDENCY_UNAVAILABLE'=>'Layanan sementara tidak tersedia. Coba lagi beberapa saat.','LOGIN_FAILED'=>'Nama pembeli atau kata sandi salah.'];
    page('Permintaan belum berhasil',empty_state('alert','Terjadi kendala',$messages[$code]??'Permintaan tidak dapat diproses.','<a class="btn btn-primary" href="/products">Kembali ke katalog</a><a class="btn" href="/cart">Lihat keranjang</a>'.(isset($_SESSION['buyer_id'])?'':'<a class="btn btn-ghost" href="/login">Masuk</a>'),true),$status,['lead'=>'Kode: '.$code]);
}
function request_data(): array {
    if(str_contains($_SERVER['CONTENT_TYPE']??'','application/json')){
        try {$data=json_decode(file_get_contents('php://input'),true,32,JSON_THROW_ON_ERROR);}
        catch(JsonException){web_error('INVALID_INPUT',400);}
        if(!is_array($data)||array_is_list($data))web_error('INVALID_INPUT',400);
        return $data;
    }
    return $_POST;
}
function require_csrf(array $data): void {
    if(!is_string($data['csrf']??null)||!hash_equals($_SESSION['csrf'],$data['csrf']))web_error('CSRF_INVALID',403);
}
function require_buyer(): int {
    if(!isset($_SESSION['buyer_id'])){if(wants_json())web_error('UNAUTHORIZED',401);redirect_to('/login');}
    $q=db()->prepare('SELECT id FROM buyers WHERE id=? AND active');$q->execute([$_SESSION['buyer_id']]);
    if(!$q->fetchColumn()){$_SESSION=[];if(wants_json())web_error('UNAUTHORIZED',401);redirect_to('/login');}
    return (int)$_SESSION['buyer_id'];
}
function storefront(string $path,string $method): never {
    session_name('NMSESSID');
    ini_set('session.use_strict_mode','1');ini_set('session.use_only_cookies','1');
    session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>($_SERVER['HTTPS']??'')==='on','path'=>'/']);
    session_start();$_SESSION['csrf']??=bin2hex(random_bytes(32));
    header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');header("Content-Security-Policy: default-src 'self'; style-src 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
    if($path==='/login'){
        if($method==='GET'||$method==='HEAD'){
            if(wants_json())json_response(['csrf'=>$_SESSION['csrf']]);
            page('Masuk pembeli',login_html(),200,['custom_head'=>true]);
        }
        if($method==='POST'){
            $data=request_data();require_csrf($data);
            if(!is_string($data['username']??null)||!is_string($data['password']??null)||strlen($data['username'])>100||strlen($data['password'])>200)web_error('INVALID_INPUT',400);
            $q=db()->prepare('SELECT id,password_hash FROM buyers WHERE username=? AND active');$q->execute([$data['username']]);$buyer=$q->fetch(PDO::FETCH_ASSOC);
            if(!$buyer||!password_verify($data['password'],$buyer['password_hash']))web_error('LOGIN_FAILED',401);
            session_regenerate_id(true);$_SESSION=['buyer_id'=>(int)$buyer['id'],'csrf'=>bin2hex(random_bytes(32))];
            if(wants_json())json_response(['status'=>'ok','csrf'=>$_SESSION['csrf']]);redirect_to('/products');
        }
    }
    if($path==='/'||$path==='/products'||preg_match('~^/products/([A-Za-z0-9_-]+)$~',$path)){
        if(!in_array($method,['GET','HEAD'],true))web_error('METHOD_NOT_ALLOWED',405);
        $suffix=$path==='/'?'/products':$path;
        $data=fetch_json(getenv('CATALOG_URL').$suffix);
        if(wants_json())json_response($data);
        if(isset($data['products'])){
            page('Katalog produk',catalog_html($data['products']),200,['custom_head'=>true]);
        }
        page($data['name'],product_html($data),200,['crumbs'=>[['Katalog','/products'],[$data['name'],null]]]);
    }
    $buyer=require_buyer();
    if($path==='/lab/status'&&in_array($method,['GET','HEAD'],true)){
        session_write_close();$services=[];
        foreach(['proxy'=>'http://proxy:8080/health','storefront'=>null,'catalog-api'=>getenv('CATALOG_URL').'/health','checkout'=>getenv('CHECKOUT_URL').'/health'] as $name=>$url){
            try{$health=$url===null?readiness():fetch_json($url);$services[$name]=($health['status']??null)==='ok';}
            catch(Throwable){$services[$name]=false;}
        }
        $ok=!in_array(false,$services,true);$status=$ok?200:503;
        if(wants_json())json_response(['status'=>$ok?'ok':'error','services'=>$services],$status);
        page('Status lab',status_html($services,$ok),$status,['lead'=>'Kesehatan layanan storefront, katalog, checkout dan proxy.']);
    }
    if($path==='/logout'){
        if($method!=='POST')web_error('METHOD_NOT_ALLOWED',405);
        $data=request_data();require_csrf($data);$_SESSION=[];
        $params=session_get_cookie_params();unset($params['lifetime']);$params['expires']=time()-3600;setcookie(session_name(),'',$params);session_destroy();
        if(wants_json())json_response(['status'=>'ok']);redirect_to('/login');
    }
    if($path==='/cart'&&in_array($method,['GET','HEAD'],true)){
        $cart=cart_view($buyer);if(wants_json())json_response($cart);page('Keranjang',cart_html($cart),200,['lead'=>'Periksa jumlah produk sebelum melanjutkan ke checkout.']);
    }
    if(in_array($path,['/cart/items','/cart/items/update','/cart/items/remove'],true)){
        if($method!=='POST')web_error('METHOD_NOT_ALLOWED',405);
        $data=request_data();require_csrf($data);
        mutate_cart($buyer,$data,$path==='/cart/items'?'add':($path==='/cart/items/update'?'update':'remove'));
        if(wants_json())json_response(cart_view($buyer));redirect_to('/cart');
    }
    if($path==='/reports'&&in_array($method,['GET','HEAD'],true)){
        $q=db()->prepare('SELECT completed_count,completed_total,updated_at FROM buyer_reports WHERE buyer_id=?');$q->execute([$buyer]);$report=$q->fetch(PDO::FETCH_ASSOC);
        if(!$report)$report=['completed_count'=>0,'completed_total'=>0,'updated_at'=>null];
        $report['completed_count']=(int)$report['completed_count'];$report['completed_total']=(int)$report['completed_total'];
        if(wants_json())json_response($report);
        page('Laporan pembeli',report_html($report),200,['lead'=>'Ringkasan pesanan yang sudah selesai diproses.']);
    }
    if($path==='/checkout'){
        if(in_array($method,['GET','HEAD'],true)){
            $cart=cart_view($buyer);if(!$cart['items'])web_error('EMPTY_CART',400);
            $fingerprint=$cart['cart_id'].':'.$cart['cart_version'];$_SESSION['checkout_keys'][$fingerprint]??=bin2hex(random_bytes(16));$key=$_SESSION['checkout_keys'][$fingerprint];
            if(wants_json())json_response($cart+['checkout_key'=>$key,'csrf'=>$_SESSION['csrf']]);
            page('Checkout pembayaran dummy',checkout_html($cart,$key),200,['crumbs'=>[['Keranjang','/cart'],['Checkout',null]]]);
        }
        if($method==='POST'){
            $data=request_data();require_csrf($data);
            $key=$data['checkout_key']??($_SERVER['HTTP_CHECKOUT_KEY']??null);
            if(!is_string($key)||!preg_match('/^[A-Za-z0-9_-]{1,128}$/D',$key))web_error('INVALID_INPUT',400);
            session_write_close();
            try{$result=send_checkout(['buyer_id'=>$buyer,'cart_id'=>$data['cart_id']??null,'cart_version'=>$data['cart_version']??null],$key);}
            catch(BusinessProblem $error){web_error($error->problem,$error->status);}
            if(wants_json())json_response($result,201);redirect_to('/orders/'.$result['order_id']);
        }
        web_error('METHOD_NOT_ALLOWED',405);
    }
    if(preg_match('~^/orders/([1-9][0-9]*)$~',$path,$matches)&&in_array($method,['GET','HEAD'],true)){
        $id=positive_int($matches[1]);if(!$id)web_error('NOT_FOUND',404);$order=order_view($buyer,$id);
        if(wants_json())json_response($order);page('Pesanan #'.$order['id'],order_html($order),200,['crumbs'=>[['Pesanan','/orders'],['#'.$order['id'],null]]]);
    }
    if($path==='/orders'&&in_array($method,['GET','HEAD'],true)){
        $q=db()->prepare('SELECT id,total,currency,status,created_at FROM orders WHERE buyer_id=? ORDER BY id DESC');$q->execute([$buyer]);$orders=$q->fetchAll(PDO::FETCH_ASSOC);
        if(wants_json())json_response(['orders'=>$orders]);
        page('Pesanan',orders_html($orders),200,['lead'=>'Riwayat pesanan dan status pemrosesannya.']);
    }
    web_error('NOT_FOUND',404);
}
