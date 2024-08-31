<?php

namespace IgniterLabs\VisitorTracker;

use Igniter\Admin\DashboardWidgets\Charts;
use Igniter\Flame\Igniter;
use Igniter\System\Classes\BaseExtension;
use IgniterLabs\VisitorTracker\Classes\RepositoryManager;
use IgniterLabs\VisitorTracker\Classes\Tracker;
use IgniterLabs\VisitorTracker\Geoip\ReaderManager;
use IgniterLabs\VisitorTracker\Http\Middleware\TrackVisitor;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageView;
use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;
use Jenssegers\Agent\AgentServiceProvider;

/**
 * VisitorTracker Extension Information File.
 */
class Extension extends BaseExtension
{
    /**
     * Register method, called when the extension is first registered.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->register(AgentServiceProvider::class);

        $this->app->singleton('tracker.reader', function($app) {
            return new ReaderManager($app);
        });

        $this->app->singleton('tracker.repository.manager', function($app) {
            return new RepositoryManager(
                new PageVisit,
                new GeoIp
            );
        });

        $this->app->singleton('tracker', function($app) {
            return new Tracker(
                Settings::instance(),
                $app['tracker.repository.manager'],
                $app['request'],
                $app['session.store'],
                $app['router'],
                $app['agent'],
                $app['tracker.reader']
            );
        });

        if (!Igniter::runningInAdmin()) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(TrackVisitor::class);
        }

    }

    public function boot()
    {
        $this->app->booted(function() {
            if ((int)Settings::get('archive_time_out')) {
                Igniter::prunableModel(PageVisit::class);
            }

            $this->registerPageViewsDatasetOnChartsWidget();
        });
    }

    /**
     * Registers any admin permissions used by this extension.
     */
    public function registerPermissions(): array
    {
        return [
            'IgniterLabs.VisitorTracker.ManageSettings' => [
                'description' => 'Manage visitor tracker extension settings',
                'group' => 'igniter::system.permissions.name',
            ],
        ];
    }

    public function registerNavigation(): array
    {
        return [
            'pagevisits' => [
                'priority' => 50,
                'class' => 'pagevisits',
                'icon' => 'fa-globe',
                'href' => admin_url('igniterlabs/visitortracker/page_visits'),
                'title' => lang('igniterlabs.visitortracker::default.text_title'),
                'permission' => 'IgniterLabs.VisitorTracker.*',
            ],
        ];
    }

    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label' => 'Visitor Tracker Settings',
                'description' => 'Manage visitor tracker settings.',
                'model' => \IgniterLabs\VisitorTracker\Models\Settings::class,
                'request' => \IgniterLabs\VisitorTracker\Http\Requests\SettingsRequest::class,
                'permissions' => ['IgniterLabs.VisitorTracker.*'],
            ],
        ];
    }

    public function registerPageViewsDatasetOnChartsWidget()
    {
        Charts::registerDatasets(function() {
            return [
                'pageviews' => [
                    'label' => 'igniterlabs.visitortracker::default.views.text_title',
                    'sets' => [
                        [
                            'label' => 'igniterlabs.visitortracker::default.views.text_title',
                            'color' => '#64B5F6',
                            'model' => PageView::class,
                            'column' => 'created_at',
                        ],
                    ],
                ],
            ];
        });
    }
}
