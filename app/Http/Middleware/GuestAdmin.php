<?php

namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;

class GuestAdmin 
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
		if(Auth::user()){
			if (Auth::user()->role_id == 1)
			{
				return Redirect::to('/dashboard');
			}
		}
        return $next($request);
    }
}
