<?php

namespace IgniterLabs\VisitorTracker\Geoip;

use Exception;
use GeoIp2\WebService\Client;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Facades\Log;

class GeoIp2 extends AbstractReader
{
    /**
     * Fetch data from a remote geoapi service.
     *
     * @param string $ip
     *
     * @return $this
     */
    public function retrieve($ip)
    {
        $accountId = Settings::get('geoip_reader_maxmind_account_id');
        $licenseKey = Settings::get('geoip_reader_maxmind_license_key');

        try {
            if (!strlen($accountId) or !strlen($licenseKey)) {
                throw new Exception('Missing GeoIP account ID or license key');
            }
            $client = new Client($accountId, $licenseKey);
            $this->record = $client->city($ip);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
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
    }

    /**
     * Returns latitude from the geoip record.
     *
     * @return string
     */
    public function latitude()
    {
        return $this->record->location->latitude;
    }

    /**
     * Returns longitude from the geoip record.
     *
     * @return string
     */
    public function longitude()
    {
        return $this->record->location->longitude;
    }

    /**
     * Returns region from the geoip record.
     *
     * @return string
     */
    public function region()
    {
        return $this->record->mostSpecificSubdivision->name;
    }

    /**
     * Returns region from the geoip record.
     *
     * @return string
     */
    public function regionISOCode()
    {
        return $this->record->mostSpecificSubdivision->isoCode;
    }

    /**
     * Returns city from the geoip record.
     *
     * @return string
     */
    public function city()
    {
        return $this->record->city->name;
    }

    /**
     * Returns postal code from the geoip record.
     *
     * @return string
     */
    public function postalCode()
    {
        return $this->record->postal->code;
    }

    /**
     * Returns country from the geoip record.
     *
     * @return string
     */
    public function country()
    {
        return $this->record->country->name;
    }

    /**
     * Returns country code from the geoip record.
     *
     * @return string
     */
    public function countryISOCode()
    {
        return $this->record->country->isoCode;
    }
}
