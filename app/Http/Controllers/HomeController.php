<?php

namespace App\Http\Controllers;

use App\Helpers\FlashMessengerHelper;
use App\Http\Requests;
use App\Models\Acl\Acl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //FlashMessengerHelper::addSuccessMessage('You are successfully logged in!');

        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        $results = Acl::getByUserGroupId(Auth::user()->group_id, true, true);

        return view("home", array(
            'results' => $results
        ));
    }
}
