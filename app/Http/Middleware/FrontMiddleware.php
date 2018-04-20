<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Cache;
use Log;
use Session;

class FrontMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return app(\Overtrue\LaravelWechat\Middleware\OAuthAuthenticate::class)->handle($request, function ($request) use ($next) {
            $appid = env('WECHAT_ORIGINALID','gh_d30a13af0bc7');
            $session = session('wechat.oauth_user');
            Session::put('openid',$session['id']);
            Session::save();
            if(! Cache::has($appid.'_'.$session['id']))
            {
                try
                {
                    Cache::rememberForever($appid.'_'.$session['id'],function () use ($session) {
                        return User::where('openid', $session['id'])->firstOrFail();
                    });
                }
                catch(ModelNotFoundException $e)
                {
                    Log::info('own mabi');
                    //$this->addGuest($session);
                }
            }
            return $next($request);
        });
    }
}
