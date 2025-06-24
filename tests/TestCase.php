<?php

namespace Dainsys\FilamentHelpers\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Dainsys\FilamentHelpers\FilamentHelpersServiceProvider;
use Filament\FilamentServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Livewire\LivewireServiceProvider;

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
