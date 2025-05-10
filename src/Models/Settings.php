<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Exception\SystemException;
use Igniter\Main\Classes\ThemeManager;
use Igniter\System\Actions\SettingsModel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class Settings extends Model
{
    public array $implement = [SettingsModel::class];

    // A unique code
    public string $settingsCode = 'igniterlabs_visitortracker_settings';

    // Reference to field configuration
    public string $settingsFieldsConfig = 'settings';

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

    public function updateMaxMindDatabase(): bool
    {
        // Get settings
        $url = $this->getUpdateUrl();
        $path = $this->getDatabasePath();

        // Get header response
        $headers = get_headers($url);
        if (substr((string) $headers[0], 9, 3) !== '200') {
            throw new SystemException('Unable to download database. ('.substr((string) $headers[0], 13).')');
        }

        // Download zipped database to a system temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
        file_put_contents($tmpFile, fopen($url, 'r'));

        // Unzip and save database
        file_put_contents($path, gzopen($tmpFile, 'r'));

        // Remove temp file
        @unlink($tmpFile);

        return true;
    }

    public function getUpdateUrl()
    {
        return $this->get('update_url', 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz');
    }

    public function getDatabasePath()
    {
        return $this->get('database_path', storage_path('app/geoip.mmdb'));
    }

    public function ensureDatabaseExists(): static
    {
        if (!File::exists($this->getDatabasePath())) {
            $this->updateMaxMindDatabase();
        }

        return $this;
    }
}
