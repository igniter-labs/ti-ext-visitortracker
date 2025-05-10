<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

use Exception;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Override;
use RuntimeException;

class Ipstack extends AbstractReader
{
    /**
     * Returns an endpoint to fetch the record from.
     *
     * @param string $ip IP address to fetch geoip record for
     */
    #[Override]
    public function retrieve(string $ip): static
    {
        try {
            $response = $this->http->get($this->getEndpoint($ip));

            if ($response->getStatusCode() == 200) {
                $record = json_decode($response->getBody()->getContents());

                if (isset($record->error)) {
                    throw new RuntimeException($record->error->info);
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
     */
    protected function getEndpoint(string $ip): string
    {
        $accessKey = Settings::get('geoip_reader_ipstack_access_key');

        if ((string)$accessKey === '') {
            throw new InvalidArgumentException('Missing ipstack access key');
        }

        return sprintf('http://api.ipstack.com/%s?access_key=%s', $ip, $accessKey);
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
