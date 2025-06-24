<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

test('command runs succesfully', function() {

    $this->artisan('dainsys:make-filament-resource-test', ['model' => 'user', 'panel' => 'app'])
        ->assertExitCode(0);

});

