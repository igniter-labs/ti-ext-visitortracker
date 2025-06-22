<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Http\Middleware;

use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;

beforeEach(fn() => Settings::flushEventListeners());

it('can track visitor', function(): void {
    PageVisit::query()->truncate();
    Settings::set([
        'status' => true,
        'track_robots' => false,
    ]);

    $this->get(route('igniter.theme.home'))
        ->assertOk();

    $pageVisitQuery = PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', '/'],
    ]);
    expect($pageVisitQuery->exists())->toBeTrue();
});

it('does not track admin pages', function(): void {
    PageVisit::query()->truncate();
    Settings::set([
        'status' => true,
        'track_robots' => false,
    ]);

    actingAsSuperUser()
        ->get(route('igniter.admin.dashboard'))
        ->assertOk();

    $pageVisitQuery = PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', '/'],
    ]);
    expect($pageVisitQuery->exists())->toBeFalse();
});
