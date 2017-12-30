<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Request;
use Session;

use App\Helpers\AclManagerHelper;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * Check for permission name
     *
     * @param string $permission_name
     * @param $route_name null|string
     * @return bool
     */
    public function checkPermission($permission_name, $route_name = null)
    {
        $response = AclManagerHelper::hasPermission($permission_name, $route_name);

        return $response;
    }

}
