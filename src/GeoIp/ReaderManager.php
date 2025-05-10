<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Manager;

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
        return new GeoIp2(new HttpClient);
    }

    /**
     * Create an instance of the ipstack driver.
     */
    protected function createIpstackDriver(): Ipstack
    {
        return new Ipstack(new HttpClient);
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
