<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Http\Middleware;

use Closure;
use IgniterLabs\VisitorTracker\Classes\Tracker;
use IgniterLabs\VisitorTracker\Models\Settings;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        if (Settings::isConfigured()) {
            resolve(Tracker::class)->boot();
        }

        return $next($request);
    }
}
