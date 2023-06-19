<?php

namespace IgniterLabs\VisitorTracker\Database\Migrations;

use Admin\DashboardWidgets\Charts;
use Admin\DashboardWidgets\Onboarding;
use Admin\DashboardWidgets\Statistics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use System\DashboardWidgets\Activities;
use System\DashboardWidgets\Cache;
use System\DashboardWidgets\News;

class UpdateDashboardWidgetPropertiesOnUserPreferencesTable extends Migration
{
    public function up()
    {
        $widgets = DB::table('user_preferences')
            ->where('item', 'admin_dashboardwidgets_dashboard')
            ->value('value');

        $widgets = collect(json_decode($widgets, true))->filter(function ($properties) {
            return array_pull($properties, 'class') !== 'IgniterLabs\VisitorTracker\DashboardWidgets\PageViews';
        })->all();

        DB::table('user_preferences')
            ->where('item', 'admin_dashboardwidgets_dashboard')
            ->update(['value' => $widgets]);
    }

    public function down()
    {
    }
}
