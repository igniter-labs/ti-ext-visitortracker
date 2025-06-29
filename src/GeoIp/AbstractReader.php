<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\GeoIp;

abstract class AbstractReader
{
    /**
     * Holds record fetched from a remote geoapi service.
     */
    protected ?object $record = null;

    /**
     * Fetch data from a remote geoapi service.
     *
     * @return $this
     */
    abstract public function retrieve(string $ip): static;

    /**
     * Returns latitude from the geoip record.
     */
    abstract public function latitude(): ?string;

    /**
     * Returns longitude from the geoip record.
     */
    abstract public function longitude(): ?string;

    /**
     * Returns region from the geoip record.
     */
    abstract public function region(): ?string;

    /**
     * Returns region from the geoip record.
     */
    abstract public function regionISOCode(): ?string;

    /**
     * Returns city from the geoip record.
     */
    abstract public function city(): ?string;

    /**
     * Returns postal code from the geoip record.
     */
    abstract public function postalCode(): ?string;

    /**
     * Returns country from the geoip record.
     */
    abstract public function country(): ?string;

    /**
     * Returns country code from the geoip record.
     */
    abstract public function countryISOCode(): ?string;
}
