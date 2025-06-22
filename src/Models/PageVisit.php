<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Models;

use Carbon\Carbon;
use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Model;
use Igniter\System\Facades\Country;
use Igniter\User\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;

/**
 * PageVisit Model Class.
 */
class PageVisit extends Model
{
    use HasFactory;
    use Prunable;

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

    protected $casts = [
        'headers' => 'array',
    ];

    protected ?Agent $agentClass = null;

    protected function afterFetch()
    {
        $this->applyAgentClass();
    }

    //
    // Accessors & Mutators
    //

    public function getAccessTypeAttribute($value): string
    {
        return ucwords((string)$value);
    }

    public function getDateAddedAttribute($value): string
    {
        return time_elapsed($value);
    }

    public function getCountryNameAttribute()
    {
        return $this->geoip ? Country::getCountryNameByCode($this->geoip->country_iso_code_2) : null;
    }

    public function getCountryCityAttribute(): string
    {
        return $this->country_name.($this->geoip ? ' - '.$this->geoip->city : null);
    }

    public function getCustomerNameAttribute()
    {
        return $this->customer->full_name ?? lang('igniterlabs.visitortracker::default.text_guest');
    }

    protected function getPlatformAttribute(): ?string
    {
        if (is_null($this->agentClass)) {
            return null;
        }

        $platform = $this->agentClass->platform();

        if ($this->agentClass->isRobot()) {
            return lang('igniterlabs.visitortracker::default.text_robot')
                .' ['.$platform.']';
        }

        if ($this->agentClass->isTablet()) {
            return lang('igniterlabs.visitortracker::default.text_tablet')
                .' ['.$platform.'] ['.$this->agentClass->device().']';
        }

        if ($this->agentClass->isMobile()) {
            return lang('igniterlabs.visitortracker::default.text_tablet')
                .' ['.$platform.'] ['.$this->agentClass->device().']';
        }

        return lang('igniterlabs.visitortracker::default.text_computer')
            .' ['.$platform.']';
    }

    //
    // Scopes
    //

    public function scopeIsOnline($query, $value)
    {
        if ($value) {
            $onlineTimeOut = Settings::get('online_time_out', 5);
            $query->where('created_at', '>=', Carbon::now()->subMinutes((int)$onlineTimeOut));
        }

        return $query;
    }

    //
    // Helpers
    //

    protected function applyAgentClass()
    {
        if (empty($this->user_agent) || !$this->headers) {
            return;
        }

        $agent = new Agent;

        $agent->setUserAgent($this->user_agent);
        $agent->setHttpHeaders($this->headers);

        $this->agentClass = $agent;
    }

    public function getAgent(): ?Agent
    {
        return $this->agentClass;
    }

    /**
     * Find when a customer was last online by ip.
     */
    public function getLastOnline(string $ip): ?static
    {
        return $this->selectRaw('*, MAX(created_at) as created_at')->where('ip_address', $ip)->first();
    }

    /**
     * Return the last online dates of all customers.
     */
    public function getOnlineDates(): Collection
    {
        return $this->pluckDates('created_at');
    }

    public function prunable(): Builder
    {
        $pastMonths = Settings::get('archive_time_out', 1);

        return static::where('updated_at', '<=', now()->subMonths((int)$pastMonths));
    }
}
