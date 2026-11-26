<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
$command=$argv[1]??'';
try {
 $pdo=db();
 $pdo->beginTransaction();
 $pdo->exec("SELECT pg_advisory_xact_lock(8364421)");
 $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (version text PRIMARY KEY, checksum text NOT NULL, installed_at timestamptz NOT NULL DEFAULT now())');
 if($command==='migrate') {
  foreach(glob(__DIR__.'/../database/migrations/*.sql') as $file) {
   $version=basename($file);$checksum=hash_file('sha256',$file);
   $q=$pdo->prepare('SELECT checksum FROM schema_migrations WHERE version=?');$q->execute([$version]);$old=$q->fetchColumn();
   if($old!==false) {if(!hash_equals($old,$checksum))throw new RuntimeException('MIGRATION_CHANGED');continue;}
   $pdo->exec(file_get_contents($file));
   $pdo->prepare('INSERT INTO schema_migrations(version,checksum) VALUES(?,?)')->execute([$version,$checksum]);
  }
 } elseif($command==='seed') {
  $q=$pdo->prepare("INSERT INTO buyers(username,password_hash) VALUES (?,?) ON CONFLICT(username) DO NOTHING");
  $q->execute(['buyer1',password_hash(secret('BUYER_PASSWORD'), PASSWORD_DEFAULT)]);
  $q->execute(['buyer2',password_hash(secret('BUYER_PASSWORD'), PASSWORD_DEFAULT)]);
  $q=$pdo->prepare('INSERT INTO products(id,name,description,price,currency) VALUES(?,?,?,?,?) ON CONFLICT(id) DO NOTHING');
  $q->execute(['A','Produk A','Produk dummy A',10000,'IDR_DUMMY']);
  $q->execute(['B','Produk B','Produk dummy B',15000,'IDR_DUMMY']);
  $pdo->exec("INSERT INTO seed_versions(version) VALUES('001') ON CONFLICT DO NOTHING");
 }else{throw new RuntimeException('INVALID_COMMAND');}
 $pdo->commit();echo json_encode(['command'=>$command,'status'=>'ok'])."\n";
} catch(Throwable $e) {if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();fwrite(STDERR,json_encode(['status'=>'error','code'=>'DATABASE_COMMAND_FAILED'])."\n");exit(1);}
