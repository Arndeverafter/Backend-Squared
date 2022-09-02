<?php

namespace App\Http\Middleware;

use App\Helpers\FeedBackHelper;
use App\Helpers\UtilsHelper;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[]|null ...$guards
     * @return mixed
     *
     * Not using this middleware atm : Frontend will handle this case
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
//        $guards = empty($guards) ? [null] : $guards;
//
//        foreach ($guards as $guard) {
//            if (Auth::guard($guard)->check()) {
//                if ($request->expectsJson()) {
//                   return UtilsHelper::apiResponseConstruct('message', FeedBackHelper::ACTIVE_SESSION, 201);
//                }
//                return redirect(RouteServiceProvider::HOME);
//            }
//        }

        return $next($request);
    }
}
