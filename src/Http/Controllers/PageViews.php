<?php

namespace IgniterLabs\VisitorTracker\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;

class PageViews extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \IgniterLabs\VisitorTracker\Models\PageView::class,
            'title' => 'lang:igniterlabs.visitortracker::default.views.text_title',
            'emptyMessage' => 'lang:igniterlabs.visitortracker::default.views.text_empty',
            'defaultSort' => ['page_views', 'DESC'],
            'showCheckboxes' => false,
            'configFile' => 'pageview',
        ],
    ];

    protected null|string|array $requiredPermissions = 'IgniterLabs.VisitorTracker.*';

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
