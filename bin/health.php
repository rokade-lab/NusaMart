<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
try {echo json_encode(readiness(),JSON_THROW_ON_ERROR)."\n";}catch(Throwable $e){fwrite(STDERR,"{\"status\":\"error\",\"code\":\"DEPENDENCY_UNAVAILABLE\"}\n");exit(1);}
