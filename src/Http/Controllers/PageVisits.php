<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Http\Actions\ListController;
use IgniterLabs\VisitorTracker\Models\PageVisit;

class PageVisits extends AdminController
{
    public array $implement = [
        ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => PageVisit::class,
            'title' => 'lang:igniterlabs.visitortracker::default.text_title',
            'emptyMessage' => 'lang:igniterlabs.visitortracker::default.text_empty',
            'defaultSort' => ['last_activity', 'DESC'],
            'showCheckboxes' => false,
            'configFile' => 'pagevisit',
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
        $query->with(['geoip', 'customer'])->distinct()->groupBy('ip_address');
    }
}
