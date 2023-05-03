<?php

namespace IgniterLabs\VisitorTracker\GeoIp;

use Exception;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Facades\Log;

class Ipstack extends AbstractReader
{
    /**
     * Returns an endpoint to fetch the record from.
     *
     * @param string $ip IP address to fetch geoip record for
     *
     * @return string
     */
    public function retrieve($ip)
    {
        try {
            $response = $this->http->get($this->getEndpoint($ip));

            if ($response->getStatusCode() == 200) {
                $record = json_decode($response->getBody()->getContents());

                if (isset($record->error)) {
                    throw new Exception($record->error->info);
                }

                $this->record = isset($record->success) ? $record : null;
            }
        } catch (Exception $ex) {
            Log::error('Ipstack Error -> '.$ex->getMessage());
        }

        return $this;
    }

    /**
     * Returns an endpoint to fetch the record from.
     *
     * @param string $ip IP address to fetch geoip record for
     *
     * @return string
     */
    protected function getEndpoint($ip)
    {
        $accessKey = Settings::get('geoip_reader_ipstack_access_key');

        if (!strlen($accessKey)) {
            throw new Exception('Missing ipstack access key');
        }

        return "http://api.ipstack.com/{$ip}?access_key={$accessKey}";
    }

    /**
     * Returns latitude from the geoip record.
     *
     * @return string
     */
    public function latitude()
    {
        return $this->record->latitude;
    }

    /**
     * Returns longitude from the geoip record.
     *
     * @return string
     */
    public function longitude()
    {
        return $this->record->longitude;
    }

    /**
     * Returns region from the geoip record.
     *
     * @return string
     */
    public function region()
    {
        return $this->record->region_name;
    }

    /**
     * Returns region ISO code from the geoip record.
     *
     * @return string
     */
    public function regionISOCode()
    {
        return $this->record->region_code;
    }

    /**
     * Returns city from the geoip record.
     *
     * @return string
     */
    public function city()
    {
        return $this->record->city;
    }

    /**
     * Returns postal code from the geoip record.
     *
     * @return string
     */
    public function postalCode()
    {
        return $this->record->zip;
    }

    /**
     * Returns country from the geoip record.
     *
     * @return string
     */
    public function country()
    {
        return $this->record->country_name;
    }

    /**
     * Returns country ISO code from the geoip record.
     *
     * @return string
     */
    public function countryISOCode()
    {
        return $this->record->country_code;
    }
}
