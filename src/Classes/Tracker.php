<?php

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
    protected $config;

    protected $repositoryManager;

    protected $request;

    protected $session;

    protected $route;

    protected $agent;

    protected $reader;

    protected $booted;

    public function __construct(
        Settings $config,
        RepositoryManager $repositoryManager,
        Request $request,
        Session $session,
        Router $route,
        Agent $agent,
        ReaderManager $reader
    ) {
        $this->config = $config;
        $this->repositoryManager = $repositoryManager;
        $this->request = $request;
        $this->session = $session;
        $this->route = $route;
        $this->agent = $agent;
        $this->readerManager = $reader;

        $agent->setUserAgent($userAgent = $request->userAgent());
        $agent->setHttpHeaders($headers = $request->header());

        $reader->setDefaultDriver($config->get('geoip_reader', 'geoip2'));
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        if ($this->isTrackable()) {
            $this->track();
        }

        $this->booted = true;
    }

    public function track()
    {
        $this->repositoryManager->clearLog();

        $this->repositoryManager->createLog($this->getLogData());
    }

    public function clearOldLog()
    {
        $this->repositoryManager->clearLog();
    }

    protected function isTrackable()
    {
        return ((bool)$this->config->get('status', true))
            && $this->isTrackableIp()
            && $this->robotIsTrackable()
            && $this->routeIsTrackable()
            && $this->pathIsTrackable();
    }

    protected function isTrackableIp()
    {
        $ipAddress = $this->request->getClientIp();
        $excludeIps = $this->config->get('exclude_ips');

        return !$excludeIps
            || $this->ipNotInRanges($ipAddress, $excludeIps);
    }

    protected function robotIsTrackable()
    {
        $trackRobots = (bool)$this->config->get('track_robots', false);

        if (!$this->agent->isRobot()) {
            return true;
        }

        return $this->agent->isRobot() && $trackRobots;
    }

    protected function routeIsTrackable()
    {
        if (!$this->route) {
            return false;
        }

        $currentRouteName = $this->route->currentRouteName();
        $excludeRoutes = $this->explodeString($this->config->get('exclude_routes'));

        return !$excludeRoutes
            || !$currentRouteName
            || !$this->matchesPattern($currentRouteName, $excludeRoutes);
    }

    protected function pathIsTrackable()
    {
        $currentPath = $this->request->path();
        $excludePaths = $this->explodeString($this->config->get('exclude_paths'));

        return !$excludePaths
            || empty($currentPath)
            || !$this->matchesPattern($currentPath, $excludePaths);
    }

    protected function getLogData()
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

        if (starts_with($referer, root_url())) {
            $referer = null;
        }

        return $referer;
    }

    //
    // GeoIP
    //

    protected function getGeoIpId()
    {
        $reader = $this->readerManager->retrieve($this->request->getClientIp());

        if (!$reader->getRecord()) {
            return null;
        }

        $geoIpId = $this->repositoryManager->createGeoIp(
            $this->getGeoIpData($reader),
            ['latitude', 'longitude']
        );

        return $geoIpId;
    }

    protected function getGeoIpData(AbstractReader $reader)
    {
        return [
            'latitude' => $reader->latitude(),
            'longitude' => $reader->longitude(),
            'region' => $reader->region(),
            'city' => $reader->city(),
            'postal_code' => $reader->postalCode(),
            'country_iso_code_2' => $reader->countryISOCode(),
        ];
    }

    //
    // IP Range
    //

    protected function ipNotInRanges($ip, $excludeRange)
    {
        if (!is_array($excludeRange)) {
            $excludeRange = [$excludeRange];
        }

        foreach ($excludeRange as $range) {
            if ($this->ipInRange($ip, $range)) {
                return false;
            }
        }

        return true;
    }

    protected function ipInRange($ip, $range)
    {
        // Wildcarded range
        // 192.168.1.*
        $range = $this->ipRangeIsWildCard($range);

        // Dashed range
        //   192.168.1.1-192.168.1.100
        //   0.0.0.0-255.255.255.255
        if ($parsedRange = $this->ipRangeIsDashed($range)) {
            [$ip1, $ip2] = $parsedRange;

            return ip2long($ip) >= $ip1 && ip2long($ip) <= $ip2;
        }

        // Masked range or fixed IP
        //   192.168.17.1/16 or
        //   127.0.0.1/255.255.255.255 or
        //   10.0.0.1
        return ipv4_match_mask($ip, $range);
    }

    protected function ipRangeIsWildCard($range)
    {
        if (!str_contains($range, '-') && str_contains($range, '*')) {
            return str_replace('*', '0', $range).'-'.str_replace('*', '255', $range);
        }

        return null;
    }

    protected function ipRangeIsDashed($range)
    {
        if (count($twoIps = explode('-', $range)) == 2) {
            return $twoIps;
        }

        return null;
    }

    //
    // Helpers
    //

    protected function explodeString($string)
    {
        return array_map('trim', explode(',', str_replace("\n", ',', $string)));
    }

    protected function matchesPattern($what, $patterns)
    {
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $what)) {
                return true;
            }
        }

        return false;
    }
}
