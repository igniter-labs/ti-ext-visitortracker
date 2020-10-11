<?php

return [
    'list' => [
        'filter' => [
            'search' => [
                'prompt' => 'lang:igniterlabs.visitortracker::default.text_filter_search',
                'mode' => 'all' // or any, exact
            ],
            'scopes' => [
                'access' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.text_filter_access',
                    'type' => 'select',
                    'conditions' => 'access_type = :filtered',
                    'options' => [
                        'browser' => 'lang:igniterlabs.visitortracker::default.text_browser',
                        'mobile' => 'lang:igniterlabs.visitortracker::default.text_mobile',
                        'robot' => 'lang:igniterlabs.visitortracker::default.text_robot',
                    ],
                ],
                'date' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.text_filter_date',
                    'type' => 'date',
                    'conditions' => 'YEAR(updated_at) = :year AND MONTH(updated_at) = :month AND DAY(updated_at) = :day',
                ],
                'recent' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.text_filter_online',
                    'type' => 'checkbox',
                    'scope' => 'isOnline',
                ],
            ],
        ],
        'toolbar' => [
            'buttons' => [
                'views' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.button_page_views',
                    'class' => 'btn btn-default',
                    'href' => 'igniterlabs/visitortracker/pageviews',
                ],
                'settings' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.button_settings',
                    'class' => 'btn btn-default',
                    'href' => 'extensions/edit/igniterlabs/visitortracker/settings',
                ],
            ],
        ],
        'columns' => [
            'ip_address' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_ip',
                'type' => 'text',
                'searchable' => TRUE,
            ],
            'country_city' => [
                'label' => 'lang:admin::lang.label_name',
                'type' => 'text',
                'sortable' => FALSE,
            ],
            'page_views' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_views',
                'type' => 'text',
                'select' => 'COUNT(ip_address)',
            ],
            'customer_name' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_customer',
                'relation' => 'customer',
                'select' => 'concat(first_name, " ", last_name)',
                'searchable' => TRUE,
            ],
            'access_type' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_access',
                'type' => 'text',
                'searchable' => TRUE,
            ],
            'platform' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_platform',
                'type' => 'text',
                'searchable' => TRUE,
            ],
            'browser' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_browser',
                'type' => 'text',
                'searchable' => TRUE,
            ],
            'referrer_uri' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_referrer_url',
                'type' => 'text',
            ],
            'last_activity' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_last_activity',
                'type' => 'timesince',
                'select' => 'MAX(updated_at)',
            ],
        ],
    ],
];
