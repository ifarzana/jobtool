<?php 

namespace App\Http\Controllers\Task;

use App\Helpers\DateHelper;
use App\Helpers\FlashMessengerHelper;
use App\Http\Controllers\Controller;

use App\Managers\Task\TaskManager;
use App\Models\Client\Client;
use App\Models\Task\Task;
use App\Models\Task\TaskSettings;
use Illuminate\Support\Facades\Session;
use App\Models\User\User;

use Redirect;
use Request;
use Validator;
use Config;
use Response;

use App\Helpers\UrlHelper;
class TaskController extends Controller
{
    /**
     * Schedule maximum days range
     *
     * @var array
     */
    protected $schedule_maximum_days_range = 10;

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination_array = array(
        'paginated' => true,
        'order_by' => 'name',
        'order' => 'ASC',
        'p' => 1,
        'id' => null,
        'per_page'  => null,
        'search_by' => null
    );

    /**
     * Filter fields
     *
     * @var array
     */
    protected $filter_fields = array(
        'reference_no',
        'customer_id',
        'booking_group',
        'status_id',
        'deposit_paid',
        'api_booking'
    );

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Booking settings*/
        $this->task_settings = TaskSettings::getSettings();

        /*Schedule maximum months range*/
        $this->schedule_maximum_days_range = (int)$this->task_settings->schedule_maximum_days_range;
    }

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
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*Clients*/
        $clients = Client::lists('name', 'id')->toArray();

        $errors = array();

        $from_date = date('Y-m-d');
        $from_date = date_format(date_create($from_date), 'd-M-Y');

        if(Session::has('schedule-from-date')) {
            $from_date = Session::get('schedule-from-date');
        }

        $to_date = new \DateTime($from_date);
        $to_date->modify('+2 days');
        $to_date = $to_date->format('Y-m-d');
        $to_date = date_format(date_create($to_date), 'd-M-Y');

        if(Session::has('schedule-to-date')) {
            $to_date = Session::get('schedule-to-date');
        }

        $submitted_data = array(
            'from_date' => $from_date,
            'to_date' => $to_date
        );

        /*BEGIN POST*/
        if(Request::isMethod('post')) {


            $data = Request::input();

            /*Clear data*/
            foreach ($data as $key => $value) {
                if ((empty($value)) AND ($value !== '0')) {
                    $data[$key] = null;
                }
            }

            /*Check from date*/
            try {
                date_format(date_create($data['from_date']), 'Y-m-d');
            }
            catch (\Exception $ex) {
                $data['from_date'] = $from_date;
            }

            /*Check to date*/
            try {
                date_format(date_create($data['to_date']), 'Y-m-d');
            }
            catch (\Exception $ex) {
                $data['to_date'] = $to_date;
            }

            /*Get from configuration*/
            $non_working_hours = true;

            /*Check the requested dates*/
            if( (isset($data['from_date'])) AND (isset($data['to_date'])) ) {
                $requested_days = DateHelper::getDays(
                    date_format(date_create($data['from_date']), 'Y-m-d'),
                    date_format(date_create($data['to_date']), 'Y-m-d')
                );

                /*Check the range*/
                if(date_format(date_create($data['from_date']), 'Y-m-d') > date_format(date_create($data['to_date']), 'Y-m-d')) {

                    $data['to_date'] = new \DateTime(date_format(date_create($data['from_date']), 'Y-m-d'));
                    $data['to_date']->modify('+2 days');
                    $data['to_date'] = $data['to_date']->format('d-M-Y');

                }else{

                    /*Schedule maximum days*/
                    $schedule_maximum_days = $this->schedule_maximum_days_range;

                    if($requested_days <= 1) {

                        $data['to_date'] = new \DateTime(date_format(date_create($data['from_date']), 'Y-m-d'));
                        $data['to_date']->modify('+1 days');
                        $data['to_date'] = $data['to_date']->format('d-M-Y');
                    }
                    elseif ($requested_days > $schedule_maximum_days) {
                        $data['to_date'] = new \DateTime(date_format(date_create($data['from_date']), 'Y-m-d'));
                        $data['to_date']->modify("+".($this->schedule_maximum_days_range-1)." days");
                        $data['to_date'] = $data['to_date']->format('d-M-Y');

                        $errors = array(
                            0 => "Please select maximum ".$this->schedule_maximum_days_range." days from the date range."
                        );
                    }

                }

            }

            $submitted_data['from_date'] = date_format(date_create($data['from_date']), 'd-M-Y');
            $submitted_data['to_date'] = date_format(date_create($data['to_date']), 'd-M-Y');

            /*Store dates*/
            Session::put('schedule-from-date', $submitted_data['from_date']);
            Session::put('schedule-to-date', $submitted_data['to_date']);
        }

        return view("task.index", array(
            'paginationData' => $paginationData,
            'clients' => $clients,
            'submitted_data' => $submitted_data,
            'errors' => $errors
        ));
    }

    /**
     * View action
     *
     * @return object
     */
    public function view()
    {
        dd('in prgress');
    }
    /**
     * Edit action
     *
     * @return object
     */
    public function edit()
    {
        /*BEGIN CHECK PERMISSION*/
        if ($this->checkPermission('update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        FlashMessengerHelper::addSuccessMessage('Task successfully updated !');

        return Redirect::action('Task\TaskController@index', $paginationData);
    }

    /**
     * Delete action
     *
     * @return object
     */
    public function delete()
    {
        /*BEGIN CHECK PERMISSION*/
        if ($this->checkPermission('delete') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        FlashMessengerHelper::addSuccessMessage('Task successfully deleted !');

        return Redirect::action('Task\TaskController@index', $paginationData);


    }


    /**
     * Get schedule Tasks ajax action
     *
     * @return object
     */
    public function getScheduleTasksAjax()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return Response::json(array(
                'code'      =>  403,
                'message'   =>  'Access denied'
            ), 403);
        }
        /*END CHECK PERMISSION*/

        $data = Request::input();

        /*From date*/
        $from_date = preg_replace('/\s/', '', $data['from_date']);
        $from_date = date_format(date_create($from_date), 'Y-m-d');

        /*To date*/
        $to_date = preg_replace('/\s/', '', $data['to_date']);
        $to_date = date_format(date_create($to_date), 'Y-m-d');

        $array = array(
            'days' => DateHelper::getDatesFromRange($from_date, $to_date),
            'dates' => DateHelper::getEachHourOfDateRange($from_date, $to_date),
            'from_date' => $from_date,
            'to_date' =>  $to_date,
            'weekStart' => 1
        );

        $array['can_create'] = true;

        /*Get users*/
        $Users = User::getUsers();

        /*Get tasks*/
        $Tasks = Task::getScheduleTasks($Users, $from_date, $to_date);

        /*****************   NO BUSINESS WORKING HOURS   *************
        /*Get non business hours*/
        //$flag_non_business_hours = TaskSettings::getTaskSettings()->schedule_maximum_days_range;
        $Non_business_hours = $this->getNonBusinessHours($array['dates']);

        $array['dates'] = array();
        foreach ($Non_business_hours as $val){
            $array['dates'][] = $val;
        }

        $array['day_width'] = '650px';
        /******************************


        /*Users*/
        $users = array();

        if(count($Users) > 0) {
            foreach ($Users as $user) {

                $users[] = array(
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'colour' => $user['colour'],
                    'disabled' => $user['isActive'] ? 0 : 1,
                    'active' => $user['isActive'] ? 'Yes' : 'No',
                    'group' => $user['user_group_name']
                );
            }
        }

        $array['users'] = $users;

        /*Tasks*/
        $tasks = array();

        if(count($Tasks) > 0) {

            foreach ($Tasks as $task) {

                $task['start_date'] = $task['start_date'].'T'.$task['start_time'];
                $task['end_date'] = $task['end_date'].'T'.$task['end_time'];

                $start = new \DateTime($task['start_date']);
                $start_date = $start->format('Y-M-d');
                $start_time = $start->format('H:i');

                $end = new \DateTime($task['end_date']);
                $end_date = $end->format('Y-M-d');
                $end_time = $end->format('H:i');

                $tasks[] = array(
                    'id' => $task['id'],
                    'user_id' => $task['user_id'],
                    'from_date' =>  $task['start_date'],
                    'to_date' => $task['end_date'],
                    'user_name' => $task['user_name'],
                    'task_name' => $task['title'],
                    'status' => $task['status'],
                    'tooltip_data' => array(
                        'start_date' => $start_date,
                        'start_time' => $start_time,
                        'end_date' => $end_date,
                        'end_time' => $end_time,
                        'status' => ucfirst(str_replace('_',' ',$task['status'])),
                    )
                );

            }
        }

        $array['tasks'] = $tasks;

        /*Non business periods*/
        $non_business_periods = array();

       /* if(count($Users) > 0) {

            foreach ($Users as $user) {

                $non_business_periods[$user['id']][] = array(
                    'from_time' => '2017-07-05T00:00',
                    'to_time' => '2017-07-05T08:00',
                    'reason' => 'Non business periods',
                );
                $non_business_periods[$user['id']][] = array(
                    'from_time' => '2017-07-05T19:00',
                    'to_time' => '2017-07-05T24:00',
                    'reason' => 'Non business periods',
                );

            }
        }*/

        $array['non_business_periods'] = $non_business_periods;

        return Response::json($array);
    }

    /**
     * Schedule request ajax action
     *
     * @return object
     */
    public function scheduleRequestAjax()
    {

        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return Response::json(array(
                'code'      =>  403,
                'message'   =>  'Access denied'
            ), 403);
        }
        /*END CHECK PERMISSION*/

        $data = Request::input();

        /*User id*/
        $user_id = preg_replace('/\s/', '', $data['user_id']);

        /*From date*/
        $from_date = preg_replace('/\s/', '', $data['from_date']);
        $from_date_time = preg_replace('/\s/', '', $data['from_date_time']);

        /*To date*/
        $to_date = preg_replace('/\s/', '', $data['to_date']);
        $to_date_time = preg_replace('/\s/', '', $data['to_date_time']);

        /*Get the user*/
        $User = User::find($user_id);

        $clients = array("0"=>"A", "1"=> "B");

        /*Form*/
        $response['form'] = array(
            'user_id' => $user_id,
            'from_date' => $from_date,
            'to_date' => $to_date
        );

        $response['view'] = array(
            'user_name' => $User->name,
            'start_date' => date_format(date_create($from_date), 'd-M-Y'),
            'end_date' => date_format(date_create($to_date), 'd-M-Y'),
            'start_time' => date_format(date_create($from_date_time), 'H:i'),
            'end_time' => date_format(date_create($to_date_time), 'H:i')
        );

        /*Non-business periods*/
        $non_business_conflicts = false;

        $response['under_non_business_period'] = $non_business_conflicts;

        if($non_business_conflicts == true) {
            $response['under_non_business_period_message'] = 'The selected range overlaps a non business period';
        }else{
            $response['under_non_business_period_message'] = '';
        }

        return Response::json($response);
    }

    /**
     * Book time action
     *
     * @param $bookingManager TaskManager
     * @return object
     */
    public function bookTime(TaskManager $taskManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('create') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*Remove filter array*/
        foreach ($this->filter_fields as $field) {
            unset($this->pagination_array[$field]);
        }

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $data = Request::input();

        /*Check if data is ok*/
        $keys = array(
            'user_id',
            'from_date',
            'to_date'
        );

        foreach ($keys as $key) {
            if(!isset($data[$key])) {

                FlashMessengerHelper::addErrorMessage('There was a problem with the submitted data !');

                return Redirect::action('Task\TaskController@index', $paginationData);

            }else{
                $array[$key] = $data[$key];
            }
        }

        /*Clear data*/
        foreach ($data as $key => $value) {
            if( (empty($value)) AND ($value !== '0') ) {
                $data[$key] = null;
            }
        }

        $data['start_date'] = date_format(date_create($data['start_date']), 'Y-m-d');
        $data['start_time'] = date_format(date_create($data['start_time']), 'H:i:s');
        $data['end_date'] = date_format(date_create($data['end_date']), 'Y-m-d');
        $data['end_time'] = date_format(date_create($data['end_time']), 'H:i:s');
        $data['project_id'] = 1;
        $data['status_id'] = 1;

        Task::create($data);

        FlashMessengerHelper::addSuccessMessage('Task successfully created !');

        return Redirect::action('Task\TaskController@index', $paginationData);

    }

    /**
     * Book time action
     *
     * @param $bookingManager TaskManager
     * @return object
     */
    public function getNonBusinessHours($array)
    {
        $times = array(
            'T00:00','T01:00','T02:00','T03:00','T04:00','T05:00','T06:00', /*Morning hours*/
            'T20:00','T21:00','T22:00','T23:00' /*Evening hours*/
        );
        /*If time ends with T00:00 to T 06:00 then unset them*/
        /*If time ends with T20:00 to T 23:00 then unset them*/
        foreach ($array as $k => $value){

            $value = substr($value, -6);

            if(in_array($value, $times)){
                unset($array[$k]);
            }
        }

        return $array;

    }
}