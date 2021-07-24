<?php

namespace IgniterLabs\VisitorTracker\Controllers;

use Admin\Facades\AdminMenu;

class PageViews extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\ListController',
    ];

    public $listConfig = [
        'list' => [
            'model'          => 'IgniterLabs\VisitorTracker\Models\PageView',
            'title'          => 'lang:igniterlabs.visitortracker::default.views.text_title',
            'emptyMessage'   => 'lang:igniterlabs.visitortracker::default.views.text_empty',
            'defaultSort'    => ['page_views', 'DESC'],
            'showCheckboxes' => false,
            'configFile'     => 'pageview',
        ],
    ];

    protected $requiredPermissions = 'IgniterLabs.VisitorTracker.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('pagevisits');
    }

    public function listExtendQuery($query)
    {
        $query->with(['geoip', 'customer'])->groupBy('request_uri');
    }
}
