<?php

return [
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                ],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
            ],
        ],
        'fields' => [
            'status' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_status',
                'type' => 'switch',
                'span' => 'left',
                'default' => TRUE,
            ],
            'track_robots' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_track_robots',
                'type' => 'switch',
                'span' => 'right',
                'default' => FALSE,
            ],
            'exclude_routes' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_exclude_routes',
                'type' => 'textarea',
                'span' => 'left',
            ],
            'exclude_paths' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_exclude_paths',
                'type' => 'textarea',
                'span' => 'right',
            ],
            'exclude_ips' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_exclude_ips',
                'type' => 'textarea',
            ],
            'online_time_out' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_online_time_out',
                'type' => 'number',
                'span' => 'left',
                'default' => 5,
                'comment' => 'lang:igniterlabs.visitortracker::default.help_customer_online',
            ],
            'archive_time_out' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_archive_time_out',
                'type' => 'select',
                'span' => 'right',
                'default' => '3',
                'options' => [
                    '0' => 'lang:igniterlabs.visitortracker::default.text_never_delete',
                    '1' => 'lang:igniterlabs.visitortracker::default.text_1_month',
                    '3' => 'lang:igniterlabs.visitortracker::default.text_3_months',
                    '6' => 'lang:igniterlabs.visitortracker::default.text_6_months',
                    '12' => 'lang:igniterlabs.visitortracker::default.text_12_months',
                ],
                'comment' => 'lang:igniterlabs.visitortracker::default.help_customer_online_archive',
            ],
            'geoip_reader' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_geoip_reader',
                'type' => 'select',
                'default' => 'maxmind',
                'options' => [
                    'geoip2' => 'lang:igniterlabs.visitortracker::default.text_maxmind',
                    'ipstack' => 'lang:igniterlabs.visitortracker::default.text_ipstack',
                ],
            ],
            'geoip_reader_ipstack_access_key' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_geoip_reader_ipstack_access_key',
                'type' => 'text',
                'comment' => 'lang:igniterlabs.visitortracker::default.help_geoip_reader_ipstack',
                'trigger' => [
                    'action' => 'show',
                    'field' => 'geoip_reader',
                    'condition' => 'value[ipstack]',
                ],
            ],
            'geoip_reader_maxmind_account_id' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_geoip_reader_maxmind_account_id',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'lang:igniterlabs.visitortracker::default.help_geoip_reader_maxmind',
                'trigger' => [
                    'action' => 'show',
                    'field' => 'geoip_reader',
                    'condition' => 'value[geoip2]',
                ],
            ],
            'geoip_reader_maxmind_license_key' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_geoip_reader_maxmind_license_key',
                'type' => 'text',
                'span' => 'right',
                'trigger' => [
                    'action' => 'show',
                    'field' => 'geoip_reader',
                    'condition' => 'value[geoip2]',
                ],
            ],
        ],
        'rules' => [
            ['status', 'lang:igniterlabs.visitortracker::default.label_status', 'required|integer'],
            ['track_robots', 'lang:igniterlabs.visitortracker::default.label_track_robots', 'required|integer'],
            ['exclude_routes', 'lang:igniterlabs.visitortracker::default.label_exclude_routes', 'string'],
            ['exclude_paths', 'lang:igniterlabs.visitortracker::default.label_exclude_paths', 'string'],
            ['exclude_ips', 'lang:igniterlabs.visitortracker::default.label_exclude_ips', 'string'],
            ['online_time_out', 'lang:igniterlabs.visitortracker::default.label_online_time_out', 'required|integer'],
            ['archive_time_out', 'lang:igniterlabs.visitortracker::default.label_archive_time_out', 'required|integer'],
            ['geoip_reader', 'lang:igniterlabs.visitortracker::default.label_geoip_reader', 'required|in:geoip2,ipstack'],
            ['geoip_reader_ipstack_access_key', 'lang:igniterlabs.visitortracker::default.label_geoip_reader_ipstack_access_key', 'required_if:geoip_reader,ipstack|string'],
            ['geoip_reader_maxmind_account_id', 'lang:igniterlabs.visitortracker::default.label_geoip_reader_maxmind_account_id', 'required_if:geoip_reader,geoip2|string'],
            ['geoip_reader_maxmind_license_key', 'lang:igniterlabs.visitortracker::default.label_geoip_reader_maxmind_license_key', 'required_if:geoip_reader,geoip2|string'],
        ],
    ],
];
