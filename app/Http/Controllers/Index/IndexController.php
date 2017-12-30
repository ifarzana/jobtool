<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Redirect;

class IndexController extends Controller
{
    /**
     * Index action
     *
     * @return object
     */
    public function index()
    {
        return Redirect::action('Auth\AuthController@login');
        //return view('welcome');
    }
}
