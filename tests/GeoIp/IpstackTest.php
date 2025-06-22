<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\GeoIp;

use IgniterLabs\VisitorTracker\GeoIp\ReaderManager;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

it('retrieves ipstack data successfully when valid access key is provided', function(): void {
    Settings::set([
        'geoip_reader_ipstack_access_key' => 'valid-access-key',
    ]);

    Http::fake([
        'http://api.ipstack.com/192.168.0.1?access_key=valid-access-key' => Http::response([
            'ip' => '192.168.0.1',
            'latitude' => '12.34',
            'longitude' => '56.78',
            'region_name' => 'Region Name',
            'region_code' => 'RN',
            'city' => 'City Name',
            'zip' => '12345',
            'country_name' => 'Country Name',
            'country_code' => 'CN',
        ]),
    ]);

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('ipstack');
    $ipstack = $readerManager->retrieve('192.168.0.1');

    expect($ipstack->latitude())->toBe('12.34')
        ->and($ipstack->longitude())->toBe('56.78')
        ->and($ipstack->region())->toBe('Region Name')
        ->and($ipstack->regionISOCode())->toBe('RN')
        ->and($ipstack->city())->toBe('City Name')
        ->and($ipstack->postalCode())->toBe('12345')
        ->and($ipstack->country())->toBe('Country Name')
        ->and($ipstack->countryISOCode())->toBe('CN');
});

it('throws exception when access key is missing', function(): void {
    Settings::set([
        'geoip_reader_ipstack_access_key' => '',
    ]);
    Log::shouldReceive('error')->with('Ipstack Error -> Missing ipstack access key')->once();

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('ipstack');
    $ipstack = $readerManager->retrieve('192.168.0.1');

    expect($ipstack->latitude())->toBeNull();
});

it('logs error when ipstack retrieval fails', function(): void {
    Settings::set([
        'geoip_reader_ipstack_access_key' => 'valid-access-key',
    ]);
    Http::fake([
        'http://api.ipstack.com/192.168.0.1?access_key=valid-access-key' => Http::response([], 404),
    ]);
    Log::shouldReceive('error')->with('Ipstack Error -> Failed to retrieve geoip record')->once();

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('ipstack');
    $ipstack = $readerManager->retrieve('192.168.0.1');

    expect($ipstack->latitude())->toBeNull();
});

it('throws exception when ipstack returns an error response', function(): void {
    Settings::set([
        'geoip_reader_ipstack_access_key' => 'valid-access-key',
    ]);
    Http::fake([
        'http://api.ipstack.com/192.168.0.1?access_key=valid-access-key' => Http::response([
            'error' => ['info' => 'Invalid IP address'],
        ]),
    ]);
    Log::shouldReceive('error')->with('Ipstack Error -> Invalid IP address')->once();

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('ipstack');
    $ipstack = $readerManager->retrieve('192.168.0.1');

    expect($ipstack->latitude())->toBeNull();
});
