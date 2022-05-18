<?php

namespace IgniterLabs\VisitorTracker\Models;

use Igniter\Flame\Database\Model;

class GeoIp extends Model
{
    /**
     * @var string The database table name
     */
    protected $table = 'igniterlabs_visitortracker_geoip';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $guarded = [];
}
