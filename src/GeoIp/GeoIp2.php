<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

use Exception;
use GeoIp2\WebService\Client;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Override;

class GeoIp2 extends AbstractReader
{
    /**
     * Fetch data from a remote geoapi service.
     *
     * @return $this
     */
    #[Override]
    public function retrieve(string $ip): static
    {
        $accountId = (int)Settings::get('geoip_reader_maxmind_account_id');
        $licenseKey = (string)Settings::get('geoip_reader_maxmind_license_key');

        try {
            if (!$accountId || !strlen($licenseKey)) {
                throw new InvalidArgumentException('Missing GeoIP account ID or license key');
            }

            $client = new Client($accountId, $licenseKey);
            $this->record = $client->city($ip);
        } catch (Exception $ex) {
            Log::error('GeoIp2 Error -> '.$ex->getMessage());
        }

        return $this;
    }

    /**
     * Returns latitude from the geoip record.
     */
    public function latitude(): ?string
    {
        return $this->record?->location?->latitude;
    }

    /**
     * Returns longitude from the geoip record.
     */
    public function longitude(): ?string
    {
        return $this->record?->location?->longitude;
    }

    /**
     * Returns region from the geoip record.
     */
    public function region(): ?string
    {
        return $this->record?->mostSpecificSubdivision?->name;
    }

    /**
     * Returns region from the geoip record.
     */
    public function regionISOCode(): ?string
    {
        return $this->record?->mostSpecificSubdivision?->isoCode;
    }

    /**
     * Returns city from the geoip record.
     */
    public function city(): ?string
    {
        return $this->record?->city?->name;
    }

    /**
     * Returns postal code from the geoip record.
     */
    public function postalCode(): ?string
    {
        return $this->record?->postal?->code;
    }

    /**
     * Returns country from the geoip record.
     */
    public function country(): ?string
    {
        return $this->record?->country?->name;
    }

    /**
     * Returns country code from the geoip record.
     */
    public function countryISOCode(): ?string
    {
        return $this->record?->country?->isoCode;
    }
}
