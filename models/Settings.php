<?php

namespace IgniterLabs\VisitorTracker\Models;

use Exception;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Main\Classes\ThemeManager;

class Settings extends Model
{
    public $implement = [\System\Actions\SettingsModel::class];

    // A unique code
    public $settingsCode = 'igniterlabs_visitortracker_settings';

    // Reference to field configuration
    public $settingsFieldsConfig = 'settings';

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
        $theme = ThemeManager::instance()->getActiveTheme();
        foreach ($theme->listPages() as $page) {
            $name = $page->getBaseFileName();
            $pages[$name] = $name;
        }

        return $pages;
    }

    public function updateMaxMindDatabase()
    {
        // Get settings
        $url = $this->getUpdateUrl();
        $path = $this->getDatabasePath();

        // Get header response
        $headers = get_headers($url);
        if (substr($headers[0], 9, 3) != '200') {
            throw new Exception('Unable to download database. ('.substr($headers[0], 13).')');
        }

        // Download zipped database to a system temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
        file_put_contents($tmpFile, fopen($url, 'r'));

        // Unzip and save database
        file_put_contents($path, gzopen($tmpFile, 'r'));

        // Remove temp file
        @unlink($tmpFile);

        return TRUE;
    }

    public function getUpdateUrl()
    {
        return $this->get('update_url', 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz');
    }

    public function getDatabasePath()
    {
        return $this->get('database_path', storage_path('app/geoip.mmdb'));
    }

    public function ensureDatabaseExists()
    {
        if (!File::exists($this->getDatabasePath())) {
            $this->updateMaxMindDatabase();
        }

        return $this;
    }
}
