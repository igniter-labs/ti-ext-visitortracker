<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests;

use IgniterLabs\VisitorTracker\Extension;
use IgniterLabs\VisitorTracker\Http\Requests\SettingsRequest;
use IgniterLabs\VisitorTracker\Models\Settings;

beforeEach(function(): void {
    $this->extension = new Extension(app());
});

it('registers settings', function(): void {
    $settings = $this->extension->registerSettings();

    expect($settings)->toHaveKey('settings')
        ->and($settings['settings']['model'])->toBe(Settings::class)
        ->and($settings['settings']['request'])->toBe(SettingsRequest::class)
        ->and($settings['settings']['permissions'])->toBe(['IgniterLabs.VisitorTracker.*']);
});

it('registers permissions', function(): void {
    $permissions = $this->extension->registerPermissions();

    expect($permissions)->toHaveKey('IgniterLabs.VisitorTracker.ManageSettings')
        ->and($permissions['IgniterLabs.VisitorTracker.ManageSettings']['group'])->toBe('igniter::system.permissions.name');
});
