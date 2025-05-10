<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Classes;

use Carbon\Carbon;
use IgniterLabs\VisitorTracker\Models\GeoIp;
use IgniterLabs\VisitorTracker\Models\PageVisit as TrackerModel;
use IgniterLabs\VisitorTracker\Models\Settings;

class RepositoryManager
{
    public function __construct(protected TrackerModel $trackerModel, protected GeoIp $geoIpModel) {}

    public function createLog($log)
    {
        return $this->trackerModel->create($log);
    }

    public function createGeoIp($geoip, $keys = null)
    {
        $geoip = $this->findOrCreate($this->geoIpModel, $geoip, $keys);

        return $geoip ? $geoip->id : null;
    }

    public function findOrCreate($model, array $attributes, $keys = null)
    {
        $query = $model->newQuery();

        $keys = $keys ?: array_keys($attributes);

        foreach ($keys as $key) {
            $query = $query->where($key, $attributes[$key]);
        }

        if (!$foundModel = $query->first()) {
            $foundModel = $model->create($attributes);
        }

        if (!$foundModel->exists) {
            return null;
        }

        return $foundModel;
    }

    public function clearLog(): void
    {
        if (($pastMonths = (int)Settings::get('archive_time_out', 3)) !== 0) {
            TrackerModel::where('updated_at', '<=', Carbon::now()->subMonths($pastMonths))->delete();
        }
    }
}
