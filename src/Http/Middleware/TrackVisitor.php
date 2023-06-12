<?php

namespace IgniterLabs\VisitorTracker\Http\Middleware;

use Closure;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        app('tracker')->boot();

        return $next($request);
    }
}
