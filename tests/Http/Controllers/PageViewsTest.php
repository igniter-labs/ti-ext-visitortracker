<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Http\Controllers;

use IgniterLabs\VisitorTracker\Http\Middleware\TrackVisitor;

it('can access page views index', function(): void {
    actingAsSuperUser()
        ->withoutMiddleware(TrackVisitor::class)
        ->get(route('igniterlabs.visitortracker.page_views'))
        ->assertOk();
});
