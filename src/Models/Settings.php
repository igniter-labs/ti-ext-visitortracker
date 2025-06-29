<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Models;

use Igniter\Flame\Database\Model;
use Igniter\Main\Classes\ThemeManager;
use Igniter\System\Actions\SettingsModel;
use Illuminate\Support\Facades\Route;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed set(string|array $key, mixed $value = null)
 * @mixin SettingsModel
 */
class Settings extends Model
{
    public array $implement = [SettingsModel::class];

    // A unique code
    public string $settingsCode = 'igniterlabs_visitortracker_settings';

    // Reference to field configuration
    public string $settingsFieldsConfig = 'settings';

    public static function isConnected(): bool
    {
        return self::get('status') && (
            self::isMaxMindReaderConfigured()
            || self::isIpstackReaderConfigured()
        );
    }

    public static function isMaxMindReaderConfigured(): bool
    {
        return self::get('geoip_reader') === 'maxmind'
            && (int)self::get('geoip_reader_maxmind_account_id')
            && strlen((string)self::get('geoip_reader_maxmind_license_key'));
    }

    public static function isIpstackReaderConfigured(): bool
    {
        return self::get('geoip_reader') === 'ipstack'
            && strlen((string)self::get('geoip_reader_ipstack_access_key'));
    }

    public function listAvailableRoutes()
    {
        $routes = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            $routes[$uri] = $uri;
        }

        return $routes;
    }

    public function listAvailablePages()
    {
        $pages = [];
        $theme = resolve(ThemeManager::class)->getActiveTheme();
        foreach ($theme->listPages() as $page) {
            $name = $page->getBaseFileName();
            $pages[$name] = $name;
        }

        return $pages;
    }
}
