<?php

namespace IgniterLabs\VisitorTracker\Http\Middleware;

use Closure;
use IgniterLabs\VisitorTracker\Models\Settings;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        if (Settings::isConfigured()) {
            app('tracker')->boot();
        }

        return $next($request);
    }
}
