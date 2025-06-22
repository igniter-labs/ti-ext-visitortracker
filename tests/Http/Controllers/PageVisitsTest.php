<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Http\Controllers;

use Igniter\User\Models\Customer;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageVisit;

it('can access page visits index', function(): void {
    PageVisit::flushEventListeners();
    $customer = Customer::factory()->create();
    $geoIp = GeoIp::create([
        'latitude' => '12.3456',
        'longitude' => '65.4321',
        'region' => 'Test Region',
        'city' => 'Test City',
        'postal_code' => '12345',
        'country_iso_code_2' => 'TC',
    ]);
    PageVisit::factory()->create([
        'access_type' => 'GET',
        'request_uri' => '/',
        'ip_address' => '192.168.1.0',
        'user_agent' => 'Test User Agent',
        'geoip_id' => $geoIp->getKey(),
        'customer_id' => $customer->getKey(),
    ]);

    actingAsSuperUser()
        ->get(route('igniterlabs.visitortracker.page_visits'))
        ->assertOk();
});
