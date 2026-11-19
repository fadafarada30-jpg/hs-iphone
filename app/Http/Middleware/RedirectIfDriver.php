<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfDriver
{
    /**
     * Keep driver accounts inside their COD workspace.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isDriver() && ! $request->routeIs('logout')) {
            return redirect()->route('driver.index');
        }

        return $next($request);
    }
}
