<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Proilyxa\LaraThread\LaraThread;
use Swoole\Thread;
use Swoole\Thread\ArrayList;
use Swoole\Thread\Map;
use Swoole\Thread\Queue;

$args = Thread::getArguments();
if (empty($args)) {
    echo 'args must be passed' . PHP_EOL;
    exit(1);
}

//$_SERVER['argv'] = $args;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';
/** @var Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$runnable = $args[0];
$params = LaraThread::castMethodParam($runnable, array_splice($args, 1));
(new $runnable)->{LaraThread::$method}(...$params);

