<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

use GuzzleHttp\Client as HttpClient;

abstract class AbstractReader
{
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
    public function __construct(
        /**
         * The HTTP client instance.
         */
        protected HttpClient $http,
    ) {}

    /**
     * Fetch data from a remote geoapi service.
     *
     * @return $this
     */
    public function retrieve(string $ip)
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
     */
    abstract protected function getEndpoint(string $ip): string;

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
