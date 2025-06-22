<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

use Illuminate\Support\Manager;

/**
 * @method AbstractReader retrieve(string $ip)
 * @method string|null latitude()
 * @method string|null longitude()
 * @method string|null region()
 * @method string|null regionISOCode()
 * @method string|null city()
 * @method string|null postalCode()
 * @method string|null country()
 * @method string|null countryISOCode()
 */
class ReaderManager extends Manager
{
    /**
     * The default reader used to collect GeoIP data.
     *
     * @var string
     */
    protected $defaultReader = 'ipstack';

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultReader;
    }

    /**
     * Create an instance of the ipstack driver.
     */
    protected function createGeoip2Driver(): GeoIp2
    {
        return resolve(GeoIp2::class);
    }

    /**
     * Create an instance of the ipstack driver.
     */
    protected function createIpstackDriver(): Ipstack
    {
        return resolve(Ipstack::class);
    }

    /**
     * Set the default reader driver name.
     *
     * @param string $reader
     */
    public function setDefaultDriver($reader): void
    {
        $this->defaultReader = $reader;
    }
}
