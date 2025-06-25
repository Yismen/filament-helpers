<?php

namespace Dainsys\FilamentHelpers;

use Dainsys\FilamentHelpers\Console\MakeFilamentResourceTestCommand;
use Illuminate\Support\ServiceProvider;

class FilamentHelpersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this
                ->publishesStubs()
                ->commands([
                    MakeFilamentResourceTestCommand::class,
                ]);
        }
    }

    public function register(): void
    {
        
    }

    public function publishesStubs(): self
    {        
            // Publish stub if desired
        $this->publishes([
            __DIR__.'/../stubs/make-filament-resource-test-file.stub' => base_path('stubs/make-filament-resource-test-file.stub'),
        ], 'make-filament-resource-test-file-stubs');

        return $this;
    }
}
