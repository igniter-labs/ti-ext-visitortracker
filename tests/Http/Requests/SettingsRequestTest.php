<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Http\Requests;

use IgniterLabs\VisitorTracker\Http\Requests\SettingsRequest;

it('defines attributes for validation error messages', function(): void {
    $attributes = (new SettingsRequest)->attributes();

    expect($attributes)->toBeArray()
        ->toHaveKey('status', lang('igniterlabs.visitortracker::default.label_status'))
        ->toHaveKey('track_robots', lang('igniterlabs.visitortracker::default.label_track_robots'))
        ->toHaveKey('exclude_routes', lang('igniterlabs.visitortracker::default.label_exclude_routes'))
        ->toHaveKey('exclude_paths', lang('igniterlabs.visitortracker::default.label_exclude_paths'))
        ->toHaveKey('exclude_ips', lang('igniterlabs.visitortracker::default.label_exclude_ips'))
        ->toHaveKey('online_time_out', lang('igniterlabs.visitortracker::default.label_online_time_out'))
        ->toHaveKey('archive_time_out', lang('igniterlabs.visitortracker::default.label_archive_time_out'))
        ->toHaveKey('geoip_reader', lang('igniterlabs.visitortracker::default.label_geoip_reader'))
        ->toHaveKey('geoip_reader_ipstack_access_key', lang('igniterlabs.visitortracker::default.label_geoip_reader_ipstack_access_key'))
        ->toHaveKey('geoip_reader_maxmind_account_id', lang('igniterlabs.visitortracker::default.label_geoip_reader_maxmind_account_id'))
        ->toHaveKey('geoip_reader_maxmind_license_key', lang('igniterlabs.visitortracker::default.label_geoip_reader_maxmind_license_key'));
});

it('defines validation rules', function(): void {
    $rules = (new SettingsRequest)->rules();

    expect($rules)->toBeArray()
        ->and($rules)->toHaveKey('status', ['required', 'integer'])
        ->and($rules)->toHaveKey('track_robots', ['required', 'integer'])
        ->and($rules)->toHaveKey('exclude_routes', ['nullable', 'string'])
        ->and($rules)->toHaveKey('exclude_paths', ['nullable', 'string'])
        ->and($rules)->toHaveKey('exclude_ips', ['nullable', 'string'])
        ->and($rules)->toHaveKey('online_time_out', ['required', 'integer'])
        ->and($rules)->toHaveKey('archive_time_out', ['required', 'integer'])
        ->and($rules)->toHaveKey('geoip_reader', ['nullable', 'in:geoip2,ipstack'])
        ->and($rules)->toHaveKey('geoip_reader_ipstack_access_key', ['nullable', 'required_if:geoip_reader,ipstack', 'string'])
        ->and($rules)->toHaveKey('geoip_reader_maxmind_account_id', ['nullable', 'required_if:geoip_reader,geoip2', 'string'])
        ->and($rules)->toHaveKey('geoip_reader_maxmind_license_key', ['nullable', 'required_if:geoip_reader,geoip2', 'string']);
});
