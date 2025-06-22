<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Classes;

use IgniterLabs\VisitorTracker\Classes\Tracker;
use IgniterLabs\VisitorTracker\GeoIp\GeoIp2;
use IgniterLabs\VisitorTracker\GeoIp\ReaderManager;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Jenssegers\Agent\Agent;

beforeEach(function(): void {
    app()->forgetInstance(Tracker::class);
});

it('tracks visitor when all conditions are met', function(): void {
    Settings::set([
        'status' => true,
        'track_robots' => false,
    ]);
    $request = Request::create('/test-uri');
    app()->instance('request', $request);

    $tracker = resolve(Tracker::class);
    $tracker->boot();

    $pageVisitQuery = PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ]);
    expect($pageVisitQuery->exists())->toBeTrue()
        ->and($tracker->boot())->toBeNull();
});

it('tracks visitor with geo ip data when all conditions are met', function(): void {
    Settings::set([
        'status' => true,
        'track_robots' => false,
    ]);
    $request = Request::create('/test-uri');
    app()->instance('request', $request);
    app()->instance(ReaderManager::class, $reader = mock(ReaderManager::class))->makePartial();
    $reader->shouldReceive('retrieve')->andReturn($geoIp2 = mock(GeoIp2::class)->makePartial());
    $geoIp2->shouldReceive('hasRecord')->andReturnTrue();
    $geoIp2->shouldReceive('latitude')->andReturn('12.3456');
    $geoIp2->shouldReceive('longitude')->andReturn('65.4321');
    $geoIp2->shouldReceive('region')->andReturn('Test Region');
    $geoIp2->shouldReceive('city')->andReturn('Test City');
    $geoIp2->shouldReceive('postalCode')->andReturn('12345');
    $geoIp2->shouldReceive('countryISOCode')->andReturn('TC');

    $tracker = resolve(Tracker::class);
    $tracker->boot();

    $pageVisitQuery = PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ]);
    $geoIpQuery = GeoIp::query()->where([
        ['latitude', '12.3456'],
        ['longitude', '65.4321'],
    ]);
    expect($pageVisitQuery->exists())->toBeTrue()
        ->and($geoIpQuery->exists())->toBeTrue()
        ->and($tracker->boot())->toBeNull();
});

it('does not track visitor when tracking is disabled', function(): void {
    Settings::set([
        'status' => false,
        'track_robots' => false,
    ]);
    $request = Request::create('/test-uri');
    app()->instance('request', $request);

    resolve(Tracker::class)->boot();

    expect(PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ])->exists())->toBeFalse();
});

it('does not track visitor when IP is excluded', function(): void {
    Settings::set([
        'status' => true,
        'track_robots' => false,
        'exclude_ips' => '192.168.1.1,192.168.0.*',
    ]);
    $request = Request::create(uri: '/test-uri', server: ['REMOTE_ADDR' => '192.168.0.1']);
    app()->instance('request', $request);

    resolve(Tracker::class)->boot();

    expect(PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ])->exists())->toBeFalse();
});

it('does not track visitor when path is excluded', function(): void {
    Settings::set([
        'status' => true,
        'track_robots' => false,
        'exclude_paths' => 'test-uri',
    ]);
    $request = Request::create(uri: '/test-uri');
    app()->instance('request', $request);

    resolve(Tracker::class)->boot();

    expect(PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ])->exists())->toBeFalse();
});

it('does not track visitor when route is excluded', function(): void {
    Settings::set([
        'status' => true,
        'track_robots' => false,
        'exclude_routes' => 'test.route',
    ]);
    $request = Request::create(uri: '/test-uri');
    app()->instance('request', $request);
    Route::shouldReceive('currentRouteName')->andReturn('test.route');

    resolve(Tracker::class)->boot();

    expect(PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ])->exists())->toBeFalse();
});

it('does not track visitor when robot tracking is disabled', function(): void {
    Settings::set([
        'status' => true,
        'track_robots' => false,
    ]);
    $request = Request::create(uri: '/test-uri');
    app()->instance('request', $request);
    app()->instance('agent', $agentMock = mock(Agent::class)->makePartial());
    $agentMock->shouldReceive('isRobot')->andReturnTrue();

    resolve(Tracker::class)->boot();

    expect(PageVisit::query()->where([
        ['access_type', 'GET'],
        ['request_uri', 'test-uri'],
    ])->exists())->toBeFalse();
});
