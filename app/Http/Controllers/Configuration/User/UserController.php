<?php

namespace App\Http\Controllers\Configuration\User;

use App\Http\Controllers\Controller;
use App\Models\Domain\Domain;
use App\Models\User\Group;
use Redirect;
use Request;
use Validator;
use Auth;
use App\Models\User\User;
use App\Helpers\UrlHelper;
use App\Helpers\FlashMessengerHelper;
use App\Models\User\Session as UMSession;

class UserController extends Controller {

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

        $results = User::fetchAll($paginationData, request()->get('search_by'), array());
        
        return view("configuration.user.index", array(
            'paginationData' => $paginationData,
            'results' => $results
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
            $user = User::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Format dates*/
        if(!empty($user->dob)) {
            $user->dob = date_format(date_create($user->dob), 'd-M-Y');
        }

        if(!empty($user->joined_at)) {
            $user->joined_at = date_format(date_create($user->joined_at), 'd-M-Y');
        }

        if(!empty($user->left_at)) {
            $user->left_at = date_format(date_create($user->left_at), 'd-M-Y');
        }

        /*User groups*/
        $groups = Group::lists('name', 'id')->toArray();

        return view("configuration.user.view", array(
            'paginationData' => $paginationData,
            'result' => $user,
            'groups' => $groups,
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
            $user = User::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Format dob*/
        if(!empty($user->dob)) {
            $user->dob = date_format(date_create($user->dob), 'd-M-Y');
        }
        if(!empty($user->joined_at)) {
            $user->joined_at = date_format(date_create($user->joined_at), 'd-M-Y');
        }
        if(!empty($user->left_at)) {
            $user->left_at = date_format(date_create($user->left_at), 'd-M-Y');
        }

        /*User groups*/
        $groups = Group::lists('name', 'id')->toArray();

        /*Edit own account*/
        $edit_own_account = false;
        if(Auth::User()->id == $user->id) {
            $edit_own_account = true; 
        }
        
        /*BEGIN POST*/
        if(Request::isMethod('post')) {


            $data = Request::input();

            $rules = User::$rules;
            $messages = User::$messages;

            /*Format date*/
            if(!empty($data['dob'])) {
                $data['dob'] = date_format(date_create($data['dob']), 'Y-m-d');
            }else{
                $data['dob'] = null;
            }
            if(!empty($data['joined_at'])) {
                $data['joined_at'] = date_format(date_create($data['joined_at']), 'Y-m-d');
            }else{
                $data['joined_at'] = null;
            }
            if(!empty($data['left_at'])) {
                $data['left_at'] = date_format(date_create($data['left_at']), 'Y-m-d');
            }else{
                $data['left_at'] = null;
            }

            /*Check email*/
            if( (isset($data['id'])) AND (!empty($data['id'])) ) {
                if( User::find($data['id'])->email == $data['email'] ) {
                    $rules['email'] = 'required|max:100|email';
                }
            }

            /*Check password*/
            if(empty($data['password'])) {
                unset($data['password']);
            }else{
                $data['password'] = bcrypt($data['password']);
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\User\UserController@edit', $paginationData)->withInput()->withErrors($validator);
            }

            $user->update($data);

            /*Check if the user needs to be logged out*/
            if( (Auth::User()->id == $user->id) AND (isset($data['password'])) ) {
                return redirect('/auth/logout');
            }

            FlashMessengerHelper::addSuccessMessage('User successfully updated !');

            return Redirect::action('Configuration\User\UserController@edit', $paginationData);
        }
        /*END POST*/

        return view("configuration.user.edit", array(
            'paginationData' => $paginationData,
            'result' => $user,
            'groups' => $groups,
            'edit_own_account' => $edit_own_account
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

        /*User groups*/
        $groups = Group::lists('name', 'id')->toArray();

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            /*Encrypt password*/
            if(!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $rules = User::$rules;
            $messages = User::$messages;

            /*Update rules*/
            $rules['password'] = 'required|min:6';

            /*Format date*/
            if(!empty($data['dob'])) {
                $data['dob'] = date_format(date_create($data['dob']), 'Y-m-d');
            }else{
                $data['dob'] = null;
            }
            if(!empty($data['joined_at'])) {
                $data['joined_at'] = date_format(date_create($data['joined_at']), 'Y-m-d');
            }else{
                $data['joined_at'] = null;
            }
            if(!empty($data['left_at'])) {
                $data['left_at'] = date_format(date_create($data['left_at']), 'Y-m-d');
            }else{
                $data['left_at'] = null;
            }

            /*Check email*/
            if( (isset($data['id'])) AND (!empty($data['id'])) ) {
                if( User::find($data['id'])->email == $data['email'] ) {
                    $rules['email'] = 'required|max:100|email';
                }
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\User\UserController@create', $paginationData)->withInput()->withErrors($validator);
            }

            User::create($data);

            FlashMessengerHelper::addSuccessMessage('User successfully created!');

            return Redirect::action('Configuration\User\UserController@index', $paginationData);
        }
        /*END POST*/

        return view("configuration.user.create", array(
            'paginationData' => $paginationData,
            'groups' => $groups,
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
            $user = User::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if the logged in user is trying to delete his own account*/
        if(Auth::user()->id == $user->id) {

            FlashMessengerHelper::addWarningMessage('You cannot delete your own account !');

            return Redirect::action('Configuration\User\UserController@index', $paginationData);
        }

        try {
            $user->delete();

            FlashMessengerHelper::addSuccessMessage('User successfully deleted !');
        }
        catch (\Exception $ex) {
            
            FlashMessengerHelper::addErrorMessage('Cannot be deleted because item is in use !');
        }

        return Redirect::action('Configuration\User\UserController@index', $paginationData);
    }

    /**
     * Sessions action
     *
     * @return object
     */
    public function sessions()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /**
         * Pagination array
         *
         * @var array
         */
        $pagination_array2 = array(
        'paginated' => true,
        'order_by'  => 'created_at',
        'order'     => 'DESC',
        'p'         => 1,
        'id'        => null,
        'per_page'  => null,
        'search_by' => null
    );

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($pagination_array2);
        /*END PAGINATION & SEARCH*/

        $results = UMSession::fetchAll($paginationData, request()->get('search_by'), array());

        return view("configuration.user.sessions", array(
            'paginationData' => $paginationData,
            'results' => $results
        ));
    }

    /**
     * User's assigned domains
     *
     * @return object
     */
    public function domains()
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
            $user = User::findOrFail($id);
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the domains of a user*/
        $results = Domain::getUserDomains($user->id);

        return view("configuration.user.domains", array(
            'paginationData' => $paginationData,
            'results' => $results,
            'user' => $user
        ));
    }

}