<?php

namespace App\Helpers;

use App\Managers\Acl\AclManager;

class AclManagerHelper
{
    /**
     * Check for permission an returns true or false
     *
     * @param $privilege_name string
     * @param $route_name string|null
     *
     * @return bool
     */
    static function hasPermission($privilege_name, $route_name = null)
    {
        $request = request();

        $aclManager = new AclManager();

        /*Get route*/

        if($route_name == null) {
            $route_array = explode("/", $request->route()->getPath());
            $route_name = $route_array[0];
        }

        return $aclManager->checkPermission($route_name, $privilege_name);
        
    }
    
}