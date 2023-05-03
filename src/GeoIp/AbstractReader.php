<?php

namespace IgniterLabs\VisitorTracker\GeoIp;

use GuzzleHttp\Client as HttpClient;

abstract class AbstractReader
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Holds recorrd fetched from a remote geoapi service.
     *
     * @var object
     */
    protected $record;

    /**
     * Create a new GeoIP reader instance.
     *
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Fetch data from a remote geoapi service.
     *
     * @param string $ip
     *
     * @return $this
     */
    public function retrieve($ip)
    {
        $response = $this->http->get($this->getEndpoint($ip));
        if ($response->getStatusCode() == 200) {
            $this->record = json_decode($response->getBody()->getContents());
        }

        return $this;
    }

    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Returns an endpoint to fetch the record from.
     *
     * @param string $ip IP address to fetch geoip record for
     *
     * @return string
     */
    abstract protected function getEndpoint($ip);

    /**
     * Returns latitude from the geoip record.
     *
     * @return string
     */
    abstract public function latitude();

    /**
     * Returns longitude from the geoip record.
     *
     * @return string
     */
    abstract public function longitude();

    /**
     * Returns region from the geoip record.
     *
     * @return string
     */
    abstract public function region();

    /**
     * Returns region from the geoip record.
     *
     * @return string
     */
    abstract public function regionISOCode();

    /**
     * Returns city from the geoip record.
     *
     * @return string
     */
    abstract public function city();

    /**
     * Returns postal code from the geoip record.
     *
     * @return string
     */
    abstract public function postalCode();

    /**
     * Returns country from the geoip record.
     *
     * @return string
     */
    abstract public function country();

    /**
     * Returns country code from the geoip record.
     *
     * @return string
     */
    abstract public function countryISOCode();
}
