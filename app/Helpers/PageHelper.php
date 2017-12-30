<?php

namespace App\Helpers;
use App\Managers\Acl\AclManager;

class PageHelper
{
    /**
     * Return the page (resource) title
     *
     * @return string
     */
    static function getPageTitle()
    {
        $request = request();

        $aclManager = new AclManager();
        
        /*Get route*/
        $route_array = explode("/", $request->route()->getPath());
        $route_name = $route_array[0];

        return $aclManager->getResourceName($route_name);
    }
    
}