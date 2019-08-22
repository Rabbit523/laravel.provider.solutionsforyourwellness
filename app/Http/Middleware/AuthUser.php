<?php

namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;

class AuthUser
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
			return Redirect::route('userlogin');
		}	
		
		// For Admin not acces front
		/* if(Auth::user()->user_role_id  == SUPER_ADMIN_ROLE_ID){
			return Redirect::to('/admin');
		} */
		
        return $next($request);
    }
}
