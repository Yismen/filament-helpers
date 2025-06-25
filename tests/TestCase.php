<?php

namespace Dainsys\FilamentHelpers\Tests;

use Dainsys\FilamentHelpers\FilamentHelpersServiceProvider;
use Filament\FilamentServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function getPackageProviders($app)
    {
        return [
            FilamentHelpersServiceProvider::class,
            FilamentServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }
}
