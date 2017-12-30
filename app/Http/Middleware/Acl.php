<?php 

namespace App\Http\Middleware;

use App\Managers\Acl\AclManager;
use Closure;

class Acl {

    /**
     * AclManager
     *
     * @var object
     */
    protected $aclManager;
    
    /**
     * Construct - set the $aclManager
     *
     * @param $aclManager AclManager
     * @return void
     */
    function __construct(AclManager $aclManager)
    {
        $this->aclManager = $aclManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*Get route*/
        $route = $request->route();

        $route_array = explode("/", $route->getPath());

        $route_name = $route_array[0];

        if($this->aclManager->checkRoute($route_name) == false) {
            abort(404);
        } else {

            if($this->aclManager->getUser() != null) {

                if($this->aclManager->checkAcl($route_name) == false) {
                    abort(401);
                }

            }

        }

        return $next($request);
    }


}