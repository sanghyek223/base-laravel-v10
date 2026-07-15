<?php

namespace App\Http\Middleware\Site;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 세션값 관리
        $routeName = $request->route()->getName();

        if (!$request->ajax() && !empty($routeName)) {

        }

        return $next($request);
    }
}
