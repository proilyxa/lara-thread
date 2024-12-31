<?php

declare(strict_types=1);

namespace Proilyxa\LaraThread;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('lara-thread');

        $this->publishes([
            __DIR__ . '/lara-thread.php' => base_path('lara-thread.php')
        ], 'proilyxa-lara-thread');
    }
}
