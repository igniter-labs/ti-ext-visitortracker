<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Models;

use Igniter\Flame\Database\Model;
use Igniter\User\Models\Customer;

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

    public $timestamps = true;

    public $relation = [
        'belongsTo' => [
            'geoip' => [GeoIp::class],
            'customer' => [Customer::class, 'foreignKey' => 'customer_id'],
        ],
    ];
}
