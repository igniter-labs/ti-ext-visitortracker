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
