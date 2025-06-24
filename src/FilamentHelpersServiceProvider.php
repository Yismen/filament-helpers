<?php

namespace Dainsys\FilamentHelpers;

use Dainsys\FilamentHelpers\Console\Commands\MakeFilamentHelpers;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentHelpersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFilamentHelpers::class,
            ]);
        }
    }

    public function register(): void
    {
        
    }
}
