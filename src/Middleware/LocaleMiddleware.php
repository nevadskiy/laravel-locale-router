<?php

namespace Nevadskiy\LocalizationRouter\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use View;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TODO: all operations with locale...

        // TODO: CHECK WHERE SHOULD BE USED LocaleRepository.Get method and decide where it should be set

        return $next($request);
    }
}
