<?php
declare(strict_types=1);
require __DIR__.'/../app/worker.php';
require __DIR__.'/../app/queue.php';
run_worker('queue-worker','queue_batch',$argv);
