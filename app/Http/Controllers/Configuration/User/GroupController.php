<?php 

namespace App\Http\Controllers\Configuration\User;

use App\Helpers\FlashMessengerHelper;
use App\Http\Controllers\Controller;
use App\Models\Acl\Acl;
use App\Models\Acl\Permission;
use App\Models\Acl\Privilege;
use App\Models\Acl\PrivilegeAvailability;
use App\Models\Acl\Resource;
use App\Models\User\Group;
use Redirect;
use Request;
use Validator;
use Auth;
use App\Helpers\UrlHelper;

class GroupController extends Controller {

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination_array = array(
        'paginated' => true,
        'order_by'  => 'name',
        'order'     => 'ASC',
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
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $results = Group::fetchAll($paginationData, request()->get('search_by'), array());
        
        return view("configuration.user.group.index", array(
            'paginationData' => $paginationData,
            'results' => $results
        ));
    }

    /**
     * Edit action
     *
     * @return object
     */
    public function edit()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        try {
            $group = Group::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = Group::$rules;
            $messages = Group::$messages;

            /*Check name*/
            if( (isset($data['id'])) AND (!empty($data['id'])) ) {
                if( strtolower(Group::find($data['id'])->name) == strtolower($data['name']) ) {
                    $rules['name'] = 'required|max:45';
                }
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\User\GroupController@edit', $paginationData)->withInput()->withErrors($validator);
            }

            $group->update($data);

            FlashMessengerHelper::addSuccessMessage('User group successfully updated !');

            return Redirect::action('Configuration\User\GroupController@edit', $paginationData);
        }
        /*END POST*/

        return view("configuration.user.group.edit", array(
            'paginationData' => $paginationData,
            'result' => $group,
        ));
    }

    /**
     * Create action
     *
     * @return object
     */
    public function create()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('create') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = Group::$rules;
            $messages = Group::$messages;

            /*Check name*/
            if( (isset($data['id'])) AND (!empty($data['id'])) ) {
                if( strtolower(Group::find($data['id'])->name) == strtolower($data['name']) ) {
                    $rules['name'] = 'required|max:45';
                }
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\User\GroupController@create', $paginationData)->withInput()->withErrors($validator);
            }

            Group::create($data);

            FlashMessengerHelper::addSuccessMessage('User group successfully created!');

            return Redirect::action('Configuration\User\GroupController@index', $paginationData);
        }
        /*END POST*/

        return view("configuration.user.group.create", array(
            'paginationData' => $paginationData
        ));
    }

    /**
     * Delete action
     *
     * @return object
     */
    public function delete()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('delete') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        try {
            $group = Group::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if the group is locked*/
        if($group->locked == 1) {

            return redirect('/auth/logout');
        }

        try {
            $group->delete();

            FlashMessengerHelper::addSuccessMessage('User group successfully deleted !');
        }
        catch (\Exception $ex) {

            FlashMessengerHelper::addErrorMessage('Cannot be deleted because item is in use !');
        }

        return Redirect::action('Configuration\User\GroupController@index', $paginationData);
    }

    /**
     * Index action
     *
     * @return object
     */
    public function permissions()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        try {
            $group = Group::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if the group is locked*/
        if($group->locked == 1) {
            return redirect('/auth/logout');
        }

        $allowedResources = array();

        $allowedResourcesArray = Acl::GetAllowedByUserGroupId($group->id)->toArray();

        foreach ($allowedResourcesArray as $ar) {
            $allowedResources[$ar['resource_id']] = 1;
        }

        $resources = Resource::all()->sortBy('name ASC')->toArray();

        foreach ($resources as $key => $resource) {

            $resources[$key]['allowed'] = false;

            if(isset($allowedResources[$resource['id']])) {
                $resources[$key]['allowed'] = true;
            }

        }

        $allPrivilegeAvailability = PrivilegeAvailability::all()->sortBy(array(
            'resource_id' => 'ASC',
            'privilege_id' => 'ASC',
        ))->toArray();

        $PrivilegeAvailabilityArray = array();

        foreach ($allPrivilegeAvailability as $pa) {
            $PrivilegeAvailabilityArray[$pa['resource_id']][$pa['privilege_id']] = $pa['privilege_id'];
        }


        $AllPrivilegesArray = array();

        $allPrivileges = Privilege::all()->toArray();

        foreach ($allPrivileges as $privilege) {
            $AllPrivilegesArray[$privilege['id']] = array(
                'name' => $privilege['privilege'],
                'icon' => $privilege['icon']
            );
        }

        $permissionsArray = array();

        $permissions = Permission::getByUserGroupId($group->id)->toArray();

        if(count($permissions)) {

            foreach ($permissions as $permission) {

                if($permission['default_resource'])  {
                    $permission['name'] = 'N/A';
                }

                $permissionsArray[$permission['resource_id']][] = ucfirst($permission['name']);

            }

        }

        return view("configuration.user.group.permissions", array(
            'paginationData' => $paginationData,
            'group' => $group,
            'resources' => $resources,
            'PrivilegeAvailabilityArray' => $PrivilegeAvailabilityArray,
            'AllPrivilegesArray' => $AllPrivilegesArray,
            'permissionsArray' => $permissionsArray
        ));
    }

    /**
     * Change resource action
     *
     * @return object
     */
    public function changeResource()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray(
            array(
                'paginated' => true,
                'order_by'  => 'name',
                'order'     => 'ASC',
                'p'         => 1,
                'id'        => null,
                'search_by' => null
            ));
        /*END PAGINATION & SEARCH*/

        /*Check group*/
        $group_id = request()->get('group_id');

        try {
            $group = Group::findOrFail($group_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if the group is locked*/
        if($group->locked == 1) {
            return redirect('/auth/logout');
        }

        /*Check resource*/
        $resource_id = request()->get('resource_id');

        try {
            $resource = Resource::findOrFail($resource_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        $acl = Acl::CheckByUserGroupIdAndResourceId($group->id, $resource->id);


        if($acl != null) {
            /*Record exists in the db => delete record*/

            /*First delete all the permissions*/
            Permission::where(array('resource_id' => $resource->id, 'user_group_id' => $group->id))->delete();

            $acl->delete();
        }else{
            /*Record does not exist in the db => create record*/
            Acl::create(
                array(
                    'user_group_id' => $group->id,
                    'resource_id' => $resource->id
                ));

            /*Check if resource is default and add read permission*/

            if($resource->default == 1) {
                Permission::create(
                    array(
                        'user_group_id' => $group->id,
                        'resource_id' => $resource->id,
                        'privilege_id' => 2
                    ));
            }

        }

        FlashMessengerHelper::addSuccessMessage('User group permission successfully updated !');

        return Redirect::action('Configuration\User\GroupController@permissions', $paginationData);
    }

    /**
     * Change permission action
     *
     * @return object
     */
    public function changePermission()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray(
            array(
                'paginated' => true,
                'order_by'  => 'name',
                'order'     => 'ASC',
                'p'         => 1,
                'id'        => null,
                'search_by' => null
            ));
        /*END PAGINATION & SEARCH*/

        /*Check group*/
        $group_id = request()->get('group_id');

        try {
            $group = Group::findOrFail($group_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if the group is locked*/
        if($group->locked == 1) {
            return redirect('/auth/logout');
        }

        /*Check resource*/
        $resource_id = request()->get('resource_id');

        try {
            $resource = Resource::findOrFail($resource_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check privilege*/
        $privilege_id = request()->get('privilege_id');

        try {
            $privilege = Privilege::findOrFail($privilege_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        $permission = Permission::checkByUserGroupIdResourceIdAndPrivilegeId($group->id, $resource->id, $privilege->id);

        if($permission != null) {
            /*Record exists in the db => delete record*/

            $permission->delete();
        }else{
            /*Record does not exist in the db => create record*/
            Permission::create(
                array(
                    'user_group_id' => $group->id,
                    'resource_id' => $resource->id,
                    'privilege_id' => $privilege->id
                ));
        }

        FlashMessengerHelper::addSuccessMessage('User group permission successfully updated !');

        return Redirect::action('Configuration\User\GroupController@permissions', $paginationData);
    }

}