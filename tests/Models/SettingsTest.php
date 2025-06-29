<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Models;

use IgniterLabs\VisitorTracker\Models\Settings;

it('returns all available routes', function(): void {
    expect((new Settings)->listAvailableRoutes())->toBeArray();
});

it('returns all available pages from active theme', function(): void {
    expect((new Settings)->listAvailablePages())->toBeArray();
});
