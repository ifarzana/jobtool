<?php 

namespace App\Http\Controllers\Project;

use App\Helpers\UrlHelper;
use App\Http\Controllers\Controller;

use Redirect;
use Request;
use Validator;
use Config;
use Response;

class ProjectController extends Controller
{
    /**
     * Index action
     *
     * @return object
     */
    public function index()
    {
        /*BEGIN CHECK PERMISSION*/
        if ($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray(
            array(
                'paginated' => true,
                'order_by' => 'name',
                'order' => 'ASC',
                'p' => 1,
                'id' => null,
                'search_by' => null
            ));
        /*END PAGINATION & SEARCH*/

        $results = null;
            //Client::fetchAll($paginationData, request()->get('search_by'), array());

        return view("project.index", array(
            'paginationData' => $paginationData,
            'results' => $results
        ));

    }

  }