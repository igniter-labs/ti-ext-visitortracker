<?php

return [
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                    'data-progress-indicator' => 'admin::lang.text_saving',
                ],
            ],
        ],
        'fields' => [
            'status' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_status',
                'type' => 'switch',
                'span' => 'left',
                'default' => true,
            ],
            'track_robots' => [
                'label' => 'lang:igniterlabs.visitortracker::default.label_track_robots',
                'type' => 'switch',
                'span' => 'right',
                'default' => false,
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
                'default' => '1',
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
    ],
];
