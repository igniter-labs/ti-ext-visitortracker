<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\GeoIp;

use IgniterLabs\VisitorTracker\GeoIp\ReaderManager;
use IgniterLabs\VisitorTracker\Models\Settings;

it('retrieves geoip data successfully when valid account ID and license key are provided', function(): void {
    Settings::set([
        'geoip_reader_maxmind_account_id' => 12345,
        'geoip_reader_maxmind_license_key' => 'valid-license-key',
    ]);

    $readerManager = resolve(ReaderManager::class);
    $readerManager->setDefaultDriver('geoip2');
    $geoIp = $readerManager->retrieve('127.0.0.1');

    expect($geoIp->regionISOCode())->toBeNull()
        ->and($geoIp->country())->toBeNull();
});
