<?php

namespace App\Http\Middleware;

use App\Models\Settings\Settings;
use Closure;
use Request;
use Response;
use App;

class AuthenticateApi
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
        $token = $request->get('token');

        /*Global settings*/
        $global_settings = Settings::getSettings();

        if(!empty($token)) {
            $token = preg_replace('/\s/', '', $token);

            if($token != $global_settings->api_token) {
                return Response::json(array(
                    'code'      =>  403,
                    'message'   =>  'Forbidden'
                ), 403);
            }

        }else{
            return Response::json(array(
                'code'      =>  403,
                'message'   =>  'Forbidden'
            ), 403);
        }

        return $next($request);
    }
}
