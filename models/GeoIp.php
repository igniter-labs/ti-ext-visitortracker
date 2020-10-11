<?php

namespace IgniterLabs\VisitorTracker\Models;

use Model;

class GeoIp extends Model
{
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

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
