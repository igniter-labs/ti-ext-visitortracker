<?php

namespace IgniterLabs\VisitorTracker\Controllers;

use Admin\Facades\AdminMenu;

class PageVisits extends \Admin\Classes\AdminController
{
    public $implement = [
        \Admin\Actions\ListController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \IgniterLabs\VisitorTracker\Models\PageVisit::class,
            'title' => 'lang:igniterlabs.visitortracker::default.text_title',
            'emptyMessage' => 'lang:igniterlabs.visitortracker::default.text_empty',
            'defaultSort' => ['last_activity', 'DESC'],
            'showCheckboxes' => false,
            'configFile' => 'pagevisit',
        ],
    ];

    protected $requiredPermissions = 'IgniterLabs.VisitorTracker.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('pagevisits');
    }

    public function index()
    {
        app('tracker')->clearOldLog();

        $this->asExtension('ListController')->index();
    }

    public function listExtendQuery($query)
    {
        $query->with(['geoip', 'customer'])->distinct()->groupBy('ip_address');
    }
}
