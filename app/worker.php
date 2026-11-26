<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
function run_worker(string $workload, callable $batch, array $arguments): never {
    if(array_diff(array_slice($arguments,1),['--once'])){fwrite(STDERR,"{\"error\":\"INVALID_ARGUMENT\"}\n");exit(2);}
    $once=in_array('--once',$arguments,true);$running=true;
    pcntl_async_signals(true);
    pcntl_signal(SIGTERM,function()use(&$running){$running=false;});
    pcntl_signal(SIGINT,function()use(&$running){$running=false;});
    do {
        try {$result=$batch(db());echo json_encode(['workload'=>$workload,'status'=>'ok']+$result,JSON_THROW_ON_ERROR)."\n";}
        catch(Throwable $e) {fwrite(STDERR,json_encode(['workload'=>$workload,'status'=>'error','code'=>'DEPENDENCY_UNAVAILABLE'])."\n");exit(1);}
        if(!$once && $running)sleep(2);
    } while(!$once && $running);
    exit(0);
}
