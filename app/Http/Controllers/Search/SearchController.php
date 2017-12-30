<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Helpers\UrlHelper;
use Request;

class SearchController extends Controller {

    /**
     * The main search function
     *
     * @return mixed
     */
    public function index()
    {
        if(Request::isMethod('post')) {

            if (Request::has('search')) {

                $search = Request::input('search');
                $controller = Request::input('controller');
                $action = Request::input('action');

                $params = array(
                    'search_by' => urlencode($search)
                );

                foreach (UrlHelper::getKeysToInclude() as $key) {

                    if(!empty(Request::input($key))) {
                        $params[$key] = Request::input($key);
                    }
                }

                return redirect()->action($controller. '@' .$action, $params);

            }

        }

        return abort(401);
    }

}