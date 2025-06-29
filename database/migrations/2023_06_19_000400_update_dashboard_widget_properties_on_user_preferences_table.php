<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateDashboardWidgetPropertiesOnUserPreferencesTable extends Migration
{
    public function up(): void
    {
        $widgets = DB::table('admin_user_preferences')
            ->where('item', 'admin_dashboardwidgets_dashboard')
            ->value('value');

        $widgets = collect(json_decode((string)$widgets, true))->filter(fn($properties): bool => array_pull($properties, 'class') !== 'IgniterLabs\VisitorTracker\DashboardWidgets\PageViews')->all();

        DB::table('admin_user_preferences')
            ->where('item', 'admin_dashboardwidgets_dashboard')
            ->update(['value' => $widgets]);
    }

    public function down() {}
}
