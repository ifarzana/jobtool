<?php

namespace App\Http\Controllers\Configuration\Domain;

use App\Helpers\FlashMessengerHelper;
use App\Http\Controllers\Controller;
use App\Models\Domain\DomainSettings;
use Redirect;
use Request;
use Validator;

class DomainSettingController extends Controller {

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

        return Redirect::action('Configuration\Domain\DomainSettingController@view', ['id' => 1]);
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

        $id = request()->get('id');

        try {
            $DomainSettings = DomainSettings::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        return view("configuration.domain.view", array(
            'result' => $DomainSettings,
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

        $id = request()->get('id');

        try {
            $DomainSettings = DomainSettings::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = DomainSettings::$rules;
            $messages = DomainSettings::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\Domain\DomainSettingController@edit', array('id' => $DomainSettings->id))->withInput()->withErrors($validator);
            }

            $DomainSettings->update($data);

            FlashMessengerHelper::addSuccessMessage('Domain settings successfully updated !');

            return Redirect::action('Configuration\Domain\DomainSettingController@edit', array('id' => $DomainSettings->id));
        }
        /*END POST*/

        return view("configuration.domain.edit", array(
            'result' => $DomainSettings,
        ));
    }
}