<?php

namespace App\Http\Middleware;

use Closure;

class JsonApiMiddleware
{
	/**
 	 * The HTTP methods parsed by this middleware.
 	 *
 	 * All other requests will be passed on wihtout changes.
 	 */
	const PARSED_METHODS = [
        'POST', 'PUT', 'PATCH'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if (in_array($request->getMethod(), self::PARSED_METHODS)) {
			// merge the json data directly into the normal input.
			// This is necessary because FormRequest uses Request::all() to get the input data, so it ignores JSON
			$jsonArray = json_decode(utf8_encode($request->getContent()), true);
			$request->merge(empty($jsonArray) ? [] : $jsonArray);
        }

        return $next($request);
    }
}
