<?php

namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;

class AuthAdmin
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if (Auth::guest())
		{
			return Redirect::to('login');
		}
		if(Auth::user()->role_id  == 0){
			return Redirect::to('/');
		}
        return $next($request);
    }
}
