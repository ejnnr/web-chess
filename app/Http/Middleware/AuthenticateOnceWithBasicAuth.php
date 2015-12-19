<?php namespace App\Http\Middleware;

use Auth;
use Closure;

class AuthenticateOnceWithBasicAuth
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
		// only try to authenticate if client actually tried to do so
		if ($request->header('Authorization')) {
        	return Auth::onceBasic('name') ?: $next($request);
		}
		return $next($request);
    }

}
