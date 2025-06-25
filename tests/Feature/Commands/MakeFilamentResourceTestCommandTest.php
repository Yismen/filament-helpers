<?php

use Dainsys\FilamentHelpers\Console\MakeFilamentResourceTestCommand;

it('creates correct file', function () {
    $this->artisan('dainsys:make-filament-resource-test', ['model' => $this->testModel, 'panel' => $this->testPanel])
        ->assertExitCode(0);

    $this->assertFileExists(base_path("tests/Feature/Filament/{$this->testPanel}/Resources/{$this->testModel}ResourceTest.php"));
});

it('prompts for missing model name', function () {
    $this
        ->artisan(MakeFilamentResourceTestCommand::class, ['panel' => 'app'])
        ->expectsQuestion(
            'What is the model name corresponding to the filament resource?',
            $this->testModel
        );
});

it('prompts for missing panel name', function () {
    $this
        ->artisan(MakeFilamentResourceTestCommand::class, ['model' => $this->testModel])
        ->expectsQuestion(
            'Select a panel',
            $this->testPanel
        );
});
