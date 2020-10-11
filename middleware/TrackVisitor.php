<?php

namespace IgniterLabs\VisitorTracker\Middleware;

use Closure;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        app('tracker')->boot();

        return $next($request);
    }
}
