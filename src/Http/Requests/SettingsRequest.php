<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Http\Requests;

use Igniter\System\Classes\FormRequest;
use Override;

class SettingsRequest extends FormRequest
{
    #[Override]
    public function attributes(): array
    {
        return [
            'status' => lang('igniterlabs.visitortracker::default.label_status'),
            'track_robots' => lang('igniterlabs.visitortracker::default.label_track_robots'),
            'exclude_routes' => lang('igniterlabs.visitortracker::default.label_exclude_routes'),
            'exclude_paths' => lang('igniterlabs.visitortracker::default.label_exclude_paths'),
            'exclude_ips' => lang('igniterlabs.visitortracker::default.label_exclude_ips'),
            'online_time_out' => lang('igniterlabs.visitortracker::default.label_online_time_out'),
            'archive_time_out' => lang('igniterlabs.visitortracker::default.label_archive_time_out'),
            'geoip_reader' => lang('igniterlabs.visitortracker::default.label_geoip_reader'),
            'geoip_reader_ipstack_access_key' => lang('igniterlabs.visitortracker::default.label_geoip_reader_ipstack_access_key'),
            'geoip_reader_maxmind_account_id' => lang('igniterlabs.visitortracker::default.label_geoip_reader_maxmind_account_id'),
            'geoip_reader_maxmind_license_key' => lang('igniterlabs.visitortracker::default.label_geoip_reader_maxmind_license_key'),
        ];
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'integer'],
            'track_robots' => ['required', 'integer'],
            'exclude_routes' => ['nullable', 'string'],
            'exclude_paths' => ['nullable', 'string'],
            'exclude_ips' => ['nullable', 'string'],
            'online_time_out' => ['required', 'integer'],
            'archive_time_out' => ['required', 'integer'],
            'geoip_reader' => ['nullable', 'in:geoip2,ipstack'],
            'geoip_reader_ipstack_access_key' => ['nullable', 'required_if:geoip_reader,ipstack', 'string'],
            'geoip_reader_maxmind_account_id' => ['nullable', 'required_if:geoip_reader,geoip2', 'string'],
            'geoip_reader_maxmind_license_key' => ['nullable', 'required_if:geoip_reader,geoip2', 'string'],
        ];
    }
}
