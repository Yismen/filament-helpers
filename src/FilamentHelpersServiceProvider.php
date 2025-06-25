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
                ->publishesConfig()
                ->commands([
                    MakeFilamentResourceTestCommand::class,
                ]);
        }
    }

    public function register(): void {}

    protected function publishesStubs(): self
    {
        // Publish stub if desired
        $this->publishes([
            __DIR__.'/../stubs/' => base_path(config('dainsys-filament-helpers.stubs_publishes_dir', 'stubs/dainsys/')),
        ], 'dainsys-filament-helpers-stubs');

        return $this;
    }

    protected function publishesConfig(): self
    {
        $this->publishes([
            __DIR__.'/../config/dainsys-filament-helpers.php' => base_path('config/dainsys-filament-helpers.php'),
        ], 'dainsys-filament-helpers-config');

        return $this;
    }
}
