<?php
declare(strict_types=1);
function secret(string $name): string {
    $file = getenv($name . '_FILE');
    if (!$file || !is_readable($file)) throw new RuntimeException('SECRET_UNAVAILABLE');
    $value = trim(file_get_contents($file));
    if ($value === '') throw new RuntimeException('SECRET_UNAVAILABLE');
    return $value;
}
function db(): PDO {
    return new PDO('pgsql:host=' . (getenv('DB_HOST') ?: 'postgres') . ';port=5432;dbname=' . (getenv('DB_NAME') ?: 'nusamart') . ';connect_timeout=2', getenv('DB_USER') ?: 'nusamart', secret('DB_PASSWORD'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES=>false]);
}
function json_response(array $value, int $status=200): never {
    http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode($value, JSON_THROW_ON_ERROR); exit;
}
function fetch_json(string $url): array {
    $context=stream_context_create(['http'=>['timeout'=>2,'ignore_errors'=>true]]);
    $body=@file_get_contents($url, false, $context);
    $status=503;if(preg_match('~^HTTP/\S+ (\d+)~',$http_response_header[0]??'',$match))$status=(int)$match[1];
    if($status===404)throw new OutOfBoundsException('NOT_FOUND');
    if ($body===false||$status!==200) throw new RuntimeException('DEPENDENCY_UNAVAILABLE');
    return json_decode($body,true,512,JSON_THROW_ON_ERROR);
}
function safe_error(string $event): void {
    error_log(json_encode(['workload'=>getenv('WORKLOAD_ID'),'event'=>$event,'code'=>'DEPENDENCY_UNAVAILABLE']));
}
function readiness(): array {
    $pdo=db();
    $pdo->query('SELECT version FROM schema_migrations LIMIT 1')->fetchColumn();
    if(getenv('WORKLOAD_ID')==='storefront') {
        fetch_json(getenv('CATALOG_URL').'/health');fetch_json(getenv('CHECKOUT_URL').'/health');
    }
    return ['status'=>'ok','workload'=>getenv('WORKLOAD_ID'),'database'=>'ready'];
}
