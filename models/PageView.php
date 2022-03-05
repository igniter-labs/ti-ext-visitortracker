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

    public $timestamps = TRUE;

    public $relation = [
        'belongsTo' => [
            'geoip' => ['IgniterLabs\VisitorTracker\Models\GeoIp'],
            'customer' => [\Admin\Models\Customers_model::class, 'foreignKey' => 'customer_id'],
        ],
    ];
}
