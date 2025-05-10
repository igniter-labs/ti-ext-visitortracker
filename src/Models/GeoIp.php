<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Models;

use Igniter\Flame\Database\Model;

class GeoIp extends Model
{
    /**
     * @var string The database table name
     */
    protected $table = 'igniterlabs_visitortracker_geoip';

    public $timestamps = true;
}
