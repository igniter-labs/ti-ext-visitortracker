<?php

namespace IgniterLabs\VisitorTracker\Models;

use Igniter\Flame\Database\Model;

/**
 * PageVisit Model Class.
 */
class PageView extends Model
{
    /**
     * @var string The database table name
     */
    protected $table = 'igniterlabs_visitortracker_tracker';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'activity_id';

    protected $guarded = [];

    public $timestamps = true;

    public $relation = [
        'belongsTo' => [
            'geoip' => [\IgniterLabs\VisitorTracker\Models\GeoIp::class],
            'customer' => [\Igniter\User\Models\Customer::class, 'foreignKey' => 'customer_id'],
        ],
    ];
}
