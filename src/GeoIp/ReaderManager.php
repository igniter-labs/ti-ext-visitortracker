<?php

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
     *
     * @return \IgniterLabs\VisitorTracker\Geoip\GeoIp2
     */
    protected function createGeoip2Driver()
    {
        return new GeoIp2(new HttpClient);
    }

    /**
     * Create an instance of the ipstack driver.
     *
     * @return \IgniterLabs\VisitorTracker\Geoip\Ipstack
     */
    protected function createIpstackDriver()
    {
        return new Ipstack(new HttpClient);
    }

    /**
     * Set the default reader driver name.
     *
     * @param string $reader
     *
     * @return void
     */
    public function setDefaultDriver($reader)
    {
        $this->defaultReader = $reader;
    }
}
