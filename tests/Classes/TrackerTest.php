<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Classes;

use IgniterLabs\VisitorTracker\Classes\Tracker;
use IgniterLabs\VisitorTracker\GeoIp\MaxMind;
use IgniterLabs\VisitorTracker\GeoIp\ReaderManager;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Jenssegers\Agent\Agent;

beforeEach(function(): void {
    app()->forgetInstance(Tracker::class);
});

it('tracks visitor when all conditions are met', function(): void {
    Http::fake([
        'http://api.ipstack.com/192.168.0.1?access_key=valid-access-key' => Http::response(),
    ]);
    Settings::set([
        'status' => true,
        'track_robots' => false,
        'geoip_reader' => 'ipstack',
        'geoip_reader_ipstack_access_key' => 'valid-access-key',
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
        'geoip_reader' => 'maxmind',
        'geoip_reader_maxmind_account_id' => '12345',
        'geoip_reader_maxmind_license_key' => 'valid-license-key',
    ]);
    $request = Request::create('/test-uri');
    app()->instance('request', $request);
    app()->instance(ReaderManager::class, $reader = mock(ReaderManager::class))->makePartial();
    $reader->shouldReceive('retrieve')->andReturn($maxMind = mock(MaxMind::class)->makePartial());
    $maxMind->shouldReceive('hasRecord')->andReturnTrue();
    $maxMind->shouldReceive('latitude')->andReturn('12.3456');
    $maxMind->shouldReceive('longitude')->andReturn('65.4321');
    $maxMind->shouldReceive('region')->andReturn('Test Region');
    $maxMind->shouldReceive('city')->andReturn('Test City');
    $maxMind->shouldReceive('postalCode')->andReturn('12345');
    $maxMind->shouldReceive('countryISOCode')->andReturn('TC');

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
