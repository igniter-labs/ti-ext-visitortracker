<?php

namespace IgniterLabs\VisitorTracker\DashboardWidgets;

use Admin\Classes\BaseDashboardWidget;
use Admin\Traits\HasChartDatasets;
use IgniterLabs\VisitorTracker\Models\PageView;

/**
 * PageViews dashboard widget.
 */
class PageViews extends BaseDashboardWidget
{
    use HasChartDatasets;

    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'pageviews';

    protected $datasetOptions = [
        'label'           => null,
        'data'            => [],
        'fill'            => false,
        'backgroundColor' => null,
        'borderColor'     => null,
    ];

    public function initialize()
    {
        $this->setProperty('rangeFormat', 'MMMM D, YYYY');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'label'   => 'admin::lang.dashboard.label_widget_title',
                'default' => 'igniterlabs.visitortracker::default.views.text_title',
            ],
        ];
    }

    /**
     * Renders the widget.
     */
    public function render()
    {
        return $this->makePartial('$/igniterlabs/visitortracker/dashboardwidgets/pageviews/pageviews');
    }

    protected function getDatasets($start, $end)
    {
        $config = [
            'label'  => 'igniterlabs.visitortracker::default.views.text_title',
            'color'  => '#64B5F6',
            'model'  => PageView::class,
            'column' => 'created_at',
        ];

        $result[] = $this->makeDataset($config, $start, $end);

        return $result;
    }
}
