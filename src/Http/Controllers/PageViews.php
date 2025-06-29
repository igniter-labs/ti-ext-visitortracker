<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Http\Actions\ListController;
use IgniterLabs\VisitorTracker\Models\PageView;

class PageViews extends AdminController
{
    public array $implement = [
        ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => PageView::class,
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

    public function listExtendQuery($query): void
    {
        $query->with(['geoip', 'customer'])->groupBy('request_uri');
    }
}
