<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Http\Controllers;

it('can access page views index', function(): void {
    actingAsSuperUser()
        ->get(route('igniterlabs.visitortracker.page_views'))
        ->assertOk();
});
