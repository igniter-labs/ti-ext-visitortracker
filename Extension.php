<?php

namespace IgniterLabs\VisitorTracker;

use IgniterLabs\VisitorTracker\Classes\RepositoryManager;
use IgniterLabs\VisitorTracker\Classes\Tracker;
use IgniterLabs\VisitorTracker\Geoip\ReaderManager;
use IgniterLabs\VisitorTracker\Middleware\TrackVisitor;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;
use Jenssegers\Agent\AgentServiceProvider;
use System\Classes\BaseExtension;

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
        $this->app->register(AgentServiceProvider::class);

        $this->app->singleton('tracker.reader', function ($app) {
            return new ReaderManager($app);
        });

        $this->app->singleton('tracker.repository.manager', function ($app) {
            return new RepositoryManager(
                new PageVisit(),
                new GeoIp()
            );
        });

        $this->app->singleton('tracker', function ($app) {
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

        if (!$this->app->runningInAdmin()) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(TrackVisitor::class);
        }
    }

    /**
     * Registers any admin permissions used by this extension.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'IgniterLabs.VisitorTracker.ManageSettings' => [
                'description' => 'Manage visitor tracker extension settings',
                'group' => 'module',
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'pagevisits' => [
                'priority' => 50,
                'class' => 'pagevisits',
                'icon' => 'fa-globe',
                'href' => admin_url('igniterlabs/visitortracker/pagevisits'),
                'title' => lang('igniterlabs.visitortracker::default.text_title'),
                'permission' => 'IgniterLabs.VisitorTracker.*',
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'Visitor Tracker Settings',
                'description' => 'Manage visitor tracker settings.',
                'model' => \IgniterLabs\VisitorTracker\Models\Settings::class,
                'permissions' => ['IgniterLabs.VisitorTracker.*'],
            ],
        ];
    }

    public function registerDashboardWidgets()
    {
        return [
            \IgniterLabs\VisitorTracker\DashboardWidgets\PageViews::class => [
                'label' => 'Page Views chart widget',
                'context' => 'dashboard',
            ],
        ];
    }
}
