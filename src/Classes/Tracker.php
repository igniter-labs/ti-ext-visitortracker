<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Classes;

use IgniterLabs\VisitorTracker\Geoip\AbstractReader;
use IgniterLabs\VisitorTracker\Geoip\ReaderManager;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class Tracker
{
    protected const array EXCLUDE_PATHS = [
        'horizon/*',
        '_debugbar/*',
        'broadcasting/auth',
        '_assets/*',
        'livewire/*',
    ];

    protected bool $booted = false;

    public function __construct(
        protected Settings $config,
        protected RepositoryManager $repositoryManager,
        protected Request $request,
        protected Session $session,
        protected Router $route,
        protected Agent $agent,
        protected ReaderManager $readerManager,
    ) {
        $this->agent->setUserAgent($this->request->userAgent());
        $this->agent->setHttpHeaders($this->request->header());

        $this->readerManager->setDefaultDriver($this->config->get('geoip_reader', 'geoip2'));
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        if ($this->isTrackable()) {
            $this->track();
        }

        $this->booted = true;
    }

    public function track(): void
    {
        $this->repositoryManager->createLog($this->getLogData());
    }

    protected function isTrackable(): bool
    {
        return $this->config->get('status', true)
            && $this->isTrackableIp()
            && $this->robotIsTrackable()
            && $this->routeIsTrackable()
            && $this->pathIsTrackable();
    }

    protected function isTrackableIp(): bool
    {
        $ipAddress = $this->request->getClientIp();
        $excludeIps = $this->explodeString($this->config->get('exclude_ips') ?: '');

        return !$excludeIps || $this->ipNotInRanges($ipAddress, $excludeIps);
    }

    protected function robotIsTrackable(): bool
    {
        return !$this->agent->isRobot() || $this->config->get('track_robots', false);
    }

    protected function routeIsTrackable(): bool
    {
        $currentRouteName = $this->route->currentRouteName();
        $excludeRoutes = $this->explodeString($this->config->get('exclude_routes') ?: '');

        return !$excludeRoutes
            || !$currentRouteName
            || !$this->matchesPattern($currentRouteName, $excludeRoutes);
    }

    protected function pathIsTrackable(): bool
    {
        $currentPath = $this->request->path();
        $excludePaths = $this->explodeString($this->config->get('exclude_paths') ?: '');
        $excludePaths = array_merge(self::EXCLUDE_PATHS, $excludePaths);

        return empty($currentPath)
            || !$this->matchesPattern($currentPath, $excludePaths);
    }

    protected function getLogData(): array
    {
        return [
            'session_id' => $this->session->getId(),
            'ip_address' => $this->request->getClientIp(),
            'access_type' => $this->request->method(),
            'geoip_id' => $this->getGeoIpId(),
            'request_uri' => $this->request->path(),
            'query' => $this->request->getQueryString(),
            'referrer_uri' => $this->getReferer(),
            'user_agent' => $this->request->userAgent(),
            'headers' => $this->request->headers->all(),
            'browser' => $this->agent->browser(),
        ];
    }

    //
    // Agent
    //

    protected function getReferer()
    {
        $referer = $this->request->header('referer', $this->request->header('utm_source', ''));

        return starts_with($referer, page_url('home')) ? null : $referer;
    }

    //
    // GeoIP
    //

    protected function getGeoIpId()
    {
        $reader = $this->readerManager->retrieve($this->request->getClientIp());
        if (!$geoIpData = $this->getGeoIpData($reader)) {
            return null;
        }

        return $this->repositoryManager->createGeoIp($geoIpData, ['latitude', 'longitude']);
    }

    protected function getGeoIpData(AbstractReader $reader): array
    {
        return array_filter([
            'latitude' => $reader->latitude(),
            'longitude' => $reader->longitude(),
            'region' => $reader->region(),
            'city' => $reader->city(),
            'postal_code' => $reader->postalCode(),
            'country_iso_code_2' => $reader->countryISOCode(),
        ]);
    }

    //
    // IP Range
    //

    protected function ipNotInRanges(string $ip, array $excludeRange): bool
    {
        foreach ($excludeRange as $range) {
            if ($this->ipInRange($ip, $range)) {
                return false;
            }
        }

        return true;
    }

    protected function ipInRange(string $ip, string $range)
    {
        // Wildcarded range
        // 192.168.1.*
        $range = $this->ipRangeIsWildCard($range);

        // Dashed range
        //   192.168.1.1-192.168.1.100
        //   0.0.0.0-255.255.255.255
        if ($parsedRange = $this->ipRangeIsDashed($range)) {
            [$ip1, $ip2] = $parsedRange;

            return ip2long($ip) >= ip2long($ip1) && ip2long($ip) <= ip2long($ip2);
        }

        // Masked range or fixed IP
        //   192.168.17.1/16 or
        //   127.0.0.1/255.255.255.255 or
        //   10.0.0.1
        return $this->ipv4MatchMask($ip, $range);
    }

    protected function ipRangeIsWildCard(string $range): string
    {
        if (!str_contains($range, '-') && str_contains($range, '*')) {
            return str_replace('*', '0', $range).'-'.str_replace('*', '255', $range);
        }

        return $range;
    }

    protected function ipRangeIsDashed(string $range): ?array
    {
        if (count($twoIps = explode('-', $range)) == 2) {
            return $twoIps;
        }

        return null;
    }

    protected function ipv4MatchMask(string $ip, string $network): bool
    {
        $ipv4_arr = explode('/', $network);

        if (count($ipv4_arr) == 1) {
            $ipv4_arr[1] = '255.255.255.255';
        }

        $network_long = ip2long($ipv4_arr[0]);

        $x = ip2long($ipv4_arr[1]);
        $mask = long2ip($x) === $ipv4_arr[1] ? $x : 0xFFFFFFFF << (32 - $ipv4_arr[1]);
        $ipv4_long = ip2long($ip);

        return ($ipv4_long & $mask) === ($network_long & $mask);
    }

    //
    // Helpers
    //

    protected function explodeString(string $string): array
    {
        return array_map('trim', explode(',', str_replace("\n", ',', $string)));
    }

    protected function matchesPattern($what, $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $what)) {
                return true;
            }
        }

        return false;
    }
}
