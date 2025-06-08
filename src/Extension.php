<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker;

use Igniter\Admin\DashboardWidgets\Charts;
use Igniter\Flame\Support\Facades\Igniter;
use Igniter\System\Classes\BaseExtension;
use IgniterLabs\VisitorTracker\Classes\RepositoryManager;
use IgniterLabs\VisitorTracker\Classes\Tracker;
use IgniterLabs\VisitorTracker\GeoIp\ReaderManager;
use IgniterLabs\VisitorTracker\Http\Middleware\TrackVisitor;
use IgniterLabs\VisitorTracker\Http\Requests\SettingsRequest;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageView;
use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Contracts\Http\Kernel;
use Jenssegers\Agent\AgentServiceProvider;
use Override;

/**
 * VisitorTracker Extension Information File.
 */
class Extension extends BaseExtension
{
    /**
     * Register method, called when the extension is first registered.
     */
    #[Override]
    public function register(): void
    {
        parent::register();

        $this->app->register(AgentServiceProvider::class);

        $this->app->singleton('tracker.reader', fn($app): ReaderManager => new ReaderManager($app));

        $this->app->singleton('tracker.repository.manager', fn($app): RepositoryManager => new RepositoryManager(
            new PageVisit,
            new GeoIp,
        ));

        $this->app->singleton('tracker', fn($app): Tracker => new Tracker(
            Settings::instance(),
            $app['tracker.repository.manager'],
            $app['request'],
            $app['session.store'],
            $app['router'],
            $app['agent'],
            $app['tracker.reader'],
        ));

        if (!Igniter::runningInAdmin()) {
            $this->app[Kernel::class]->pushMiddleware(TrackVisitor::class);
        }

    }

    #[Override]
    public function boot(): void
    {
        $this->app->booted(function(): void {
            if (Igniter::hasDatabase() && (int)Settings::get('archive_time_out')) {
                Igniter::prunableModel(PageVisit::class);
            }

            $this->registerPageViewsDatasetOnChartsWidget();
        });
    }

    /**
     * Registers any admin permissions used by this extension.
     */
    #[Override]
    public function registerPermissions(): array
    {
        return [
            'IgniterLabs.VisitorTracker.ManageSettings' => [
                'description' => 'Manage visitor tracker extension settings',
                'group' => 'igniter::system.permissions.name',
            ],
        ];
    }

    #[Override]
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

    #[Override]
    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label' => 'Visitor Tracker Settings',
                'description' => 'Manage visitor tracker settings.',
                'icon' => 'fa fa-map-location-dot',
                'model' => Settings::class,
                'request' => SettingsRequest::class,
                'permissions' => ['IgniterLabs.VisitorTracker.*'],
            ],
        ];
    }

    public function registerPageViewsDatasetOnChartsWidget(): void
    {
        Charts::registerDatasets(fn(): array => [
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
        ]);
    }
}
