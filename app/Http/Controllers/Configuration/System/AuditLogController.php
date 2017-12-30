<?php 

namespace App\Http\Controllers\Configuration\System;

use App\Http\Controllers\Controller;
use App\Models\Log\AuditLog;
use Redirect;
use Request;
use Validator;
use Auth;
use App\Helpers\UrlHelper;

class AuditLogController extends Controller {

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination_array = array(
        'paginated' => true,
        'order_by'  => 'id',
        'order'     => 'DESC',
        'p'         => 1,
        'id'        => null,
        'per_page'  => null,
        'search_by' => null
    );

    /**
     * Index action
     *
     * @return object
     */
    public function index()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray(
            array(
                'paginated' => true,
                'order_by' => 'id',
                'order' => 'ASC',
                'p' => 1,
                'id' => null,
                'search_by' => null
            ));
        /*END PAGINATION & SEARCH*/

        $results = AuditLog::fetchAll($paginationData, request()->get('search_by'), array());

        return view("configuration.system.audit-log.index", array(
            'paginationData' => $paginationData,
            'results' => $results,
        ));
    }

    /**
     * View action
     *
     * @return object
     */
    public function view()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        try {
            $AuditLog = AuditLog::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        $data = json_decode($AuditLog->data, true);
        $changes1 = array_diff_assoc($data['original_data'], $data['updated_data']);
        $changes2 = array_diff_assoc($data['updated_data'], $data['original_data']);

        return view("configuration.system.audit-log.view", array(
            'paginationData' => $paginationData,
            'result' => $AuditLog,
            'data' => $data,
            'changes1' => $changes1,
            'changes2' => $changes2
        ));
    }

}