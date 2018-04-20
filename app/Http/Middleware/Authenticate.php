<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // 获取session
        if ($request->session()->has('gh_admin')) {
            $admin = $request->session()->get('gh_admin');
            view()->share('admin', $admin);
        }else{
            return redirect()->guest('admin/login');
        }

        return $next($request);
    }
}
