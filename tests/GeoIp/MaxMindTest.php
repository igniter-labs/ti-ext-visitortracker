<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\GeoIp;

use IgniterLabs\VisitorTracker\GeoIp\ReaderManager;
use IgniterLabs\VisitorTracker\Models\Settings;

it('retrieves maxmind data successfully when valid account ID and license key are provided', function(): void {
    Settings::set([
        'geoip_reader_maxmind_account_id' => 12345,
        'geoip_reader_maxmind_license_key' => 'valid-license-key',
    ]);

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('maxmind');
    $geoIp = $readerManager->retrieve('127.0.0.1');

    expect($geoIp->latitude())->toBeNull()
        ->and($geoIp->longitude())->toBeNull()
        ->and($geoIp->region())->toBeNull()
        ->and($geoIp->regionISOCode())->toBeNull()
        ->and($geoIp->city())->toBeNull()
        ->and($geoIp->postalCode())->toBeNull()
        ->and($geoIp->country())->toBeNull()
        ->and($geoIp->countryISOCode())->toBeNull();
});

it('throws exception when missing account ID and license key', function(): void {
    Settings::set([
        'geoip_reader_maxmind_account_id' => '',
        'geoip_reader_maxmind_license_key' => '',
    ]);

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('maxmind');
    $geoIp = $readerManager->retrieve('127.0.0.1');

    expect($geoIp->country())->toBeNull();
});
