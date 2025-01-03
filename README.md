# Lara-thread

[![Latest Version on Packagist](https://img.shields.io/packagist/v/proilyxa/lara-thread.svg?style=flat-square)](https://packagist.org/packages/proilyxa/lara-thread)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/proilyxa/lara-thread/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/proilyxa/lara-thread/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/prolyxa/lara-thread/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ilya/lara-thread/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/proilyxa/lara-thread.svg?style=flat-square)](https://packagist.org/packages/proilyxa/lara-thread)

## Description
Since Swoole 6 it supports multithreading, so it's a great way to speed up Laravel applications!

You can use this functionality in your Laravel Octane application if you are using the Swoole driver or in console
commands.

Require: Laravel 10-11, php8.1+ ZTS (Thread safe), swoole 6.0+ extension which was compiled with the
--enable-swoole-thread parameter

https://wiki.swoole.com/en/#/thread/thread

## Installation

You can install the package via composer:

```bash
composer require proilyxa/lara-thread
```

```bash
php artisan vendor:publish --tag=proilyxa-lara-thread
```

## Usage

```php
public static function run(string $class, mixed ...$params): Thread
 ```

LaraThread::run takes as its first parameter a class that implements the run() method. The run method can have any input
parameters.

## Main

```php
use Swoole\Thread\Queue;

$start = microtime(true);

$input = new Queue();
$output = new Queue();

$d = 20;
for ($i = 0; $i < $d; $i++) {
    $input->push('https://dog.ceo/api/breeds/image/random');
}

// create workers
$t = 5;
$threads = [];
for ($threadID = 0; $threadID < $t; $threadID++) {
    $threads[] = LaraThread::run(Run::class, $threadID + 1, $input, $output);
}

$result = [];
for ($i = 0; $i < $d; $i++) {
    $result[] = $output->pop(-1);
}

dump(LaraThread::recursiveUnserialize($result));

// waiting for threads to finish
for ($i = 0; $i < count($threads); $i++) {
    $threads[$i]->join();
}

echo 'timeline: ' . round(microtime(true) - $start, 4) . ' s.';
```

## Worker

```php
<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Swoole\Thread\ArrayList;
use Swoole\Thread\Map;
use Swoole\Thread\Queue;

class Run
{
    public function run(int $id, Queue $input, Queue $output): void
    {
        while (1) {
            $site = $input->pop();
            if ($site === null) {
                echo 'Thread: ' . $id . ' finished' . PHP_EOL;
                break;
            }
            $output->push(Http::get($site)->json(), Queue::NOTIFY_ONE);
            echo 'Thread: ' . $id . '  result pushed' . PHP_EOL;
        }
    }
}

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [proilyxa](https://github.com/proilyxa)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
