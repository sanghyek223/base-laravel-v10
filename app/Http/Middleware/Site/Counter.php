<?php

namespace App\Http\Middleware\Site;

use Closure;
use Illuminate\Http\Request;
use App\Services\Admin\Stat\StatServices;

class Counter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ajax 요청 아니고 route 명 있으면서
        if (!$request->ajax() && !empty($request->route()->getName())) {
//            (new StatServices())->setCountService();
        }

        return $next($request);
    }
}
