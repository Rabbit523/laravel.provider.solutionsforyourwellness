<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\ApiTokens;
use App\Models\Users;

use App\Http\Helpers;

class WellnessAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $helper = new Helpers;

        $postdata = $request->all();
        
        if(isset($postdata['device_id']) && $postdata['device_id'] != null && isset($postdata['auth_token']) && $postdata['auth_token'] != null){
			
            $matchThese = ['device_id' => $postdata['device_id'], 'auth_token' => $postdata['auth_token']];
            $is_token_valid = ApiTokens::where($matchThese)->first();
			
            if(!$is_token_valid){
               return [
							'message' =>	'Seems you are logged in from another device, please logout and login again.',
							'code' =>	401,
						]
            }
        }else{
            echo $helper->apiResponse(401, '', ["error" => trans('api.input_params_missing')]);
            return;
        }
		
		if(isset($postdata['user_id']) && $postdata['user_id'] != null){
            $matchThese = ['id' => $postdata['user_id']];
            $user = Users::where($matchThese)->first();
			if(isset($user) && $user->count() > 0){}else{
				return json_encode(array('code' => 401, 'message' => 'No account matched with this user details.'));
			}
        }

        return $next($request);
        
    }
}
