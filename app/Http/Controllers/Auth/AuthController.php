<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UserAgentHelper;
use App\Models\Acl\Resource;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\User\Session as UMSession;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Redirect;
use Auth;
use View;
use Session;
use Config;


class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Index action
     *
     * @return object
     */
    public function index()
    {
        return Redirect::action('Auth\AuthController@login');
    }

    /**
     * Login action
     *
     * @param $request Request
     * @return mixed
     */
    public function login(Request $request)
    {
        if(Auth::User() != null) {
            return Redirect::to($this->getDefaultResource()->route);
        }

        $error = false;

        /*BEGIN POST*/
        if($request->isMethod('POST')) {

            /*Log out the user first*/
            Auth::logout();

            $credentials = array(
                'email'     => Input::get('email'),
                'password'  => Input::get('password'),
            );

            /*Try to validate the credentials*/
            $attempt = $this->validateCredentials($credentials);

            if ($attempt) {

                /*Authenticate user*/
                Auth::attempt($credentials);

                /*Get the user*/
                $user = Auth::User();

                /*Check if the user is active*/
                if($user->isActive != 1) {
                    return Redirect::to('auth/suspended-account');
                }

                /*Save session*/
                $this->saveSession($user, $request);

                return Redirect::to($this->getDefaultResource()->route);

            } else {

                $error = true;

                return Redirect::to('auth/login')->with('error', $error);
            }

        }
        /*END POST*/

        return View::make('auth/login')->with('error', $error);
    }

    /**
     * Logout action
     *
     * @return object
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::to('auth');
    }

    /**
     * Restricted area action
     *
     * @return object
     */
    public function restrictedArea()
    {
        Auth::logout();
        return View::make('auth/restricted-area');
    }

    /**
     * No permission action
     *
     * @return object
     */
    public function noPermission()
    {
        Auth::logout();
        return View::make('auth/no-permission');
    }

    /**
     * Suspended action
     *
     * @return object
     */
    public function suspendedAccount()
    {
        Auth::logout();
        return View::make('auth/suspended-account');
    }

    /**
     * Error action
     *
     * @return object
     */
    public function error()
    {
        return View::make('auth/error');
    }

    /**
     * This function will check the credentials
     *
     * @param $credentials array
     * @return bool
     */
    protected function validateCredentials($credentials)
    {
        $response = Auth::validate($credentials);
        return $response;
    }

    /**
     * Returns the default resource
     *
     * @return object
     */
    protected function getDefaultResource()
    {
        $defaultResource = Resource::where('default', 1)->first();
        return $defaultResource;
    }

    /**
     * Save the session (model)
     *
     * @param $user User
     * @param $request Request
     * @return bool
     */
    protected function saveSession(User $user, Request $request)
    {
        $ua = UserAgentHelper::getUADetails();

        $data = array(
            'user_id' => $user->id,
            'name' => $user->name,
            'email_address' => $user->email,
            'client_ip_address' => $request->ip(),
            'session_id' => Session::getId()
        );

        /*Operating system*/
        if($ua['platform'] != null) {
            $data['os'] = $ua['platform'];
        }

        /*Browser name and version*/
        if($ua['browser'] != null) {

            if($ua['version'] != null) {
                $data['browser'] = $ua['browser'] . ' ('. $ua['version'] .')';
            }else{
                $data['browser'] = $ua['browser'];
            }

        }

        UMSession::create($data);

        return true;
    }

}
