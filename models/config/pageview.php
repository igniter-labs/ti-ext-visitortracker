<?php

return [
    'list' => [
        'filter' => [
            'scopes' => [
                'date' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.text_filter_date',
                    'type' => 'date',
                    'conditions' => 'YEAR(updated_at) = :year AND MONTH(updated_at) = :month AND DAY(updated_at) = :day',
                ],
            ],
        ],
        'toolbar' => [
            'buttons' => [
                'visits' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.button_page_visits',
                    'class' => 'btn btn-default',
                    'href' => 'igniterlabs/visitortracker/pagevisits',
                ],
                'settings' => [
                    'label' => 'lang:igniterlabs.visitortracker::default.button_settings',
                    'class' => 'btn btn-default',
                    'href' => 'extensions/edit/igniterlabs/visitortracker/settings',
                ],
            ],
        ],
        'columns' => [
            'request_uri' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_request_uri',
                'type' => 'text',
            ],
            'page_views' => [
                'label' => 'lang:igniterlabs.visitortracker::default.column_views',
                'type' => 'text',
                'select' => 'COUNT(request_uri)',
            ],
        ],
    ],
];
