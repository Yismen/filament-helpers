<?php

it('creates correct file', function() {
    $this->artisan('dainsys:make-filament-resource-test', ['model' => 'user', 'panel' => 'app'])
        ->assertExitCode(0);

    $this->assertFileExists(base_path('tests/Feature/Filament/App/Resources/UserResourceTest.php'));

});

