<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

use Exception;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Facades\Http;
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
            $response = Http::get($this->getEndpoint($ip));

            throw_unless(
                $response->ok(),
                new InvalidArgumentException('Failed to retrieve geoip record'),
            );

            $record = $response->object();

            if (isset($record->error)) {
                throw new RuntimeException($record->error->info);
            }

            $this->record = $record->ip === $ip ? $record : null;
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
        throw_unless(
            $accessKey = Settings::get('geoip_reader_ipstack_access_key'),
            new InvalidArgumentException('Missing ipstack access key'),
        );

        return sprintf('http://api.ipstack.com/%s?access_key=%s', $ip, $accessKey);
    }

    /**
     * Returns latitude from the geoip record.
     */
    public function latitude(): ?string
    {
        return $this->record?->latitude;
    }

    /**
     * Returns longitude from the geoip record.
     */
    public function longitude(): ?string
    {
        return $this->record?->longitude;
    }

    /**
     * Returns region from the geoip record.
     */
    public function region(): ?string
    {
        return $this->record?->region_name;
    }

    /**
     * Returns region ISO code from the geoip record.
     */
    public function regionISOCode(): ?string
    {
        return $this->record?->region_code;
    }

    /**
     * Returns city from the geoip record.
     */
    public function city(): ?string
    {
        return $this->record?->city;
    }

    /**
     * Returns postal code from the geoip record.
     */
    public function postalCode(): ?string
    {
        return $this->record?->zip;
    }

    /**
     * Returns country from the geoip record.
     */
    public function country(): ?string
    {
        return $this->record?->country_name;
    }

    /**
     * Returns country ISO code from the geoip record.
     */
    public function countryISOCode(): ?string
    {
        return $this->record?->country_code;
    }
}
