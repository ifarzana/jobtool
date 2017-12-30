<?php

namespace App\Http\Controllers\Configuration\Task;

use App\Helpers\FlashMessengerHelper;
use App\Http\Controllers\Controller;
use App\Models\Task\TaskSettings;
use Redirect;
use Request;
use Validator;

use Config;

class TaskSettingController extends Controller {


    /**
     * Context menu items
     *
     * @var array
     */
    protected $context_menu_items = array();

    /**
     * Weekend days
     *
     * @var array
     */
    protected $weekend_days = array();

    /**
     * Business hours
     *
     * @var array
     */
    protected $business_hours = array();

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Context menu items*/
        $this->context_menu_items = Config::get('schedule')['context_menu_items'];

        /*Weekend days*/
        $this->weekend_days = Config::get('schedule')['weekend_days'];

        /*Business hours*/
        $this->business_hours = Config::get('schedule')['business_hours'];
    }

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

        return Redirect::action('Configuration\Task\TaskSettingController@view', ['id' => 1]);
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
            $TaskSettings = TaskSettings::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        return view("configuration.task.view", array(
            'result' => $TaskSettings,
            'context_menu_items' => $this->context_menu_items,
            'weekend_days' => $this->weekend_days,
            'business_hours' => $this->business_hours
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
            $TaskSettings = TaskSettings::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = TaskSettings::$rules;
            $messages = TaskSettings::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            if($data['schedule_enable_context_menu'] == 1) {
                $rules['schedule_context_menu_items'] = 'required';

                if(!empty($data['schedule_context_menu_items'])) {
                    $data['schedule_context_menu_items'] = json_encode($data['schedule_context_menu_items']);
                }

            }else{
                $data['schedule_context_menu_items'] = null;
            }


            if(!empty($data['schedule_weekend_days'])) {
                $data['schedule_weekend_days'] = json_encode($data['schedule_weekend_days']);
            }

            if(!empty($data['schedule_business_hours_from_time'])) {
                $data['schedule_business_hours_from_time'] = json_encode($data['schedule_business_hours_from_time']);
            }

            if(!empty($data['schedule_business_hours_to_time'])) {
                $data['schedule_business_hours_to_time'] = json_encode($data['schedule_business_hours_to_time']);
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\Task\TaskSettingController@edit', array('id' => $TaskSettings->id))->withInput()->withErrors($validator);
            }

            $TaskSettings->update($data);

            FlashMessengerHelper::addSuccessMessage('Task settings successfully updated !');

            return Redirect::action('Configuration\Task\TaskSettingController@edit', array('id' => $TaskSettings->id));
        }
        /*END POST*/

        return view("configuration.task.edit", array(
            'result' => $TaskSettings,
            'context_menu_items' => $this->context_menu_items,
            'weekend_days' => $this->weekend_days,
            'business_hours' => $this->business_hours
        ));
    }
}