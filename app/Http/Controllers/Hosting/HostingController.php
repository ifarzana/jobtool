<?php

namespace App\Http\Controllers\Hosting;

use App\Http\Controllers\Controller;
use App\Managers\Hosting\HostingManager;
use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Currency\Currency;
use App\Models\Domain\AssignedDomain;
use App\Models\Domain\Domain;
use App\Models\Domain\DomainRenewalEntry;
use App\Models\Domain\Interval;
use App\Models\Domain\Registrar;
use App\Models\Domain\Server;
use App\Models\Hosting\Hosting;
use App\Models\Invoice\Invoice;
use App\Models\Settings\Settings;
use Redirect;
use Request;
use Response;
use Config;
use Validator;
use Auth;
use App\Helpers\UrlHelper;
use App\Helpers\FlashMessengerHelper;

class HostingController extends Controller {

    /**
     * Currency
     *
     * @var object
     */
    protected $currency;

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination_array = array(
        'paginated' => true,
        'order_by'  => 'created_at',
        'order'     => 'DESC',
        'p'         => 1,
        'id'        => null,
        'per_page'  => null,
        'search_by' => null,
        'new_renewal' => null,
    );

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Currency*/
        $this->currency = Currency::getActiveCurrency();
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

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $client_id = request()->get('client_id');
        $user_id = request()->get('user_id');

        if($client_id) {
            try {
                $client = Client::findOrFail($client_id);
                $array = array('client_id' => $client->id);

            }
            catch (\Exception $ex) {
                return redirect('/auth/logout');
            }
        }
        else{
            $array = array();
        }

        $results = Hosting::fetchAll($paginationData, request()->get('search_by'), $array);

        return view("hosting.index", array(
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

        /*Get the hosting*/
        $id = request()->get('id');

        try {
            $hosting = Hosting::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the contact*/
        $ClientContact = ClientContact::find($hosting->contact_id);

        $contact = null;
        $no_email_address = false;

        if($ClientContact){
            if (empty($ClientContact->email_address)){
                $no_email_address = true;
            }

            $contact = $ClientContact->name.' ('.( (!empty($ClientContact->email_address) ) ? $ClientContact->email_address :'no email address').')';
        }

        /*Get the invoice*/
        $Invoice = Invoice::find($hosting->invoice_id);

        /*Format dates*/
        if(!empty($hosting->hosting_renewal_date)) {
            $hosting->hosting_renewal_date = date_format(date_create($hosting->hosting_renewal_date), 'd-M-Y');
        }

        if(!empty($hosting->registration_date)) {
            $hosting->registration_date = date_format(date_create($hosting->registration_date), 'd-M-Y');
        }

        if(!empty($hosting->expiry_date)) {
            $hosting->expiry_date = date_format(date_create($hosting->expiry_date), 'd-M-Y');
        }

        if(!empty($hosting->next_due_date)) {
            $hosting->next_due_date = date_format(date_create($hosting->next_due_date), 'd-M-Y');
        }

        /*Registrars*/
        $registrars = Registrar::lists('name', 'id')->toArray();

        /*Servers*/
        $servers = Server::lists('name', 'id')->toArray();

        /*Intervals*/
        $intervals = Interval::lists('name', 'id')->toArray();

        /*Get all the renewal entries*/
//        $renewal_entries = HostingRenewalEntry::where('hosting_id', $hosting->id)->get();

        /*Calculate the due date*/
//        $due_date = null;
        $due_date = $hosting->expiry_date;
//        if(count($renewal_entries)>0) {
//            $due_date = HostingRenewalEntry::where('hosting_id', $hosting->id)->max('expiry_date');
//        }
//        else{
//            $due_date = $hosting->expiry_date;
//        }

        return view("hosting.view", array(
            'paginationData' => $paginationData,
            'result' => $hosting,
            'contact' => $contact,
            'Invoice' => $Invoice,
            'no_email_address' => $no_email_address,
            'registrars' => $registrars,
            'servers' => $servers,
//            'renewal_entries' => $renewal_entries,
            'intervals' => $intervals,
            'due_date' => $due_date,

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
            $hosting = Hosting::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        dd('Hosting edit in progress');

        return view("hosting.renew", array(
            'paginationData' => $paginationData,
            'result' => $hosting,
        ));
    }

    /**
     * Create action
     *
     * @param $hostingManager HostingManager
     * @return object
     */
    public function create(HostingManager $hostingManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('create') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*Intervals*/
        $intervals = Interval::lists('name', 'id')->toArray();

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = Hosting::$rules;
            $messages = Hosting::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if ( ((empty($value)) AND ($value !== '0')) OR ($value == 'N/A') ) {
                    $data[$key] = null;
                }
            }

            /*Format date*/
            if(!empty($data['registration_date'])) {
                $data['registration_date'] = date_format(date_create($data['registration_date']), 'Y-m-d');
            }else{
                $data['registration_date'] = null;
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Hosting\HostingController@create', $paginationData)->withInput()->withErrors($validator);
            }

            /*BEGIN SET EXPIRY DATE*/
            $data['expiry_date'] = null;
            if ($data['registration_date'] && $data['registration_period'] && $data['interval_id']) {
                $data['expiry_date'] = $hostingManager->setTheExpiryDate($data['registration_date'], $data['registration_period'], $data['interval_id']);
            }
            /*END SET EXPIRY DATE*/

            $Hosting = Hosting::create($data);

            if($Hosting) {

                /*Generate invoice*/
                //$hostingManager->generateInvoice($Hosting);

                /*Get the logged in user id*/
                $user_id = $hostingManager->getTheLoggedInUser()->id;
                $assigned_user_id = $user_id;

                /*Assign to the logged in user*/
               // AssignedHosting::create(array('user_id'=>$user_id, 'hosting_id'=>$Hosting->id));

                /*Global settings*/
                $global_settings = Settings::getSettings();

                $data = array(
                    'token' => $global_settings->api_token,
                    'params' => json_encode(array(
                        'user_id' => $assigned_user_id,
                        'hosting_id' => $Hosting->id,
                        'key' => 'hosting-assigned',
                    ))
                );

                $query = http_build_query($data);

               // $job_url = $global_settings->app_url.'/api/hostings/send-hosting-email?'.$query;

                /*Dispatch a job when a hosting is assigned to an user
                http://laravel.local/api/domains/send-domain-email?token=4c7f6f8fa93d59c45502c0ae8c4a95b&params=%7B%22user_id%22%3A1%2C%22domain_id%22%3A47%2C%22key%22%3A%22domain-assigned%22%7D*/

                /*Dispatch job*/
               // dispatch(new SendEmail($job_url));
            }


            FlashMessengerHelper::addSuccessMessage('Hosting successfully created!');

            return Redirect::action('Hosting\HostingController@index', $paginationData);
        }
        /*END POST*/

        return view("hosting.create", array(
            'paginationData' => $paginationData,
            'intervals' => $intervals,
            'currency' => $this->currency,
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
            $hosting = Hosting::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        dd('Hosting delete in progress');

        try {
            /*Renewal entries*/
            $renewal_entries = DomainRenewalEntry::where('hosting_id', $hosting->id)->get();

            /*Assigned hostings*/
            $assigned_hostings = AssignedDomain::where('hosting_id', $hosting->id)->get();

            /*If there are renewal entries you cannot delete the hosting*/
            if ( empty (count($renewal_entries) )) {

                foreach ($assigned_hostings as $assigned_hosting){
                    $assigned_hosting->delete();
                }
            }

            $hosting->delete();

            /*Invoices*/
            $invoices = Invoice::where('hosting_id', $hosting->id)->get();

            /*If there is an invoice then delete it*/
            if ( !empty (count($invoices) )) {

                foreach ($invoices as $invoice){
                    $invoice->delete();
                }
            }

            FlashMessengerHelper::addSuccessMessage('Domain successfully deleted !');
        }
        catch (\Exception $ex) {

            FlashMessengerHelper::addErrorMessage('Cannot be deleted because item is in use !');
        }

        return Redirect::action('Domain\DomainController@index', $paginationData);
    }

    /**
     * Create Renewal Entry action
     *
     * @param $hostingManager HostingManager
     * @return object
     */
    public function createRenewalEntry(HostingManager $hostingManager)
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
        $data = Request::input();

        $rules = DomainRenewalEntry::$rules;
        $messages = DomainRenewalEntry::$messages;

        /*Format date*/
        if(!empty($data['renewal_date'])) {
            $data['renewal_date'] = date_format(date_create($data['renewal_date']), 'Y-m-d');
        }else{
            $data['renewal_date'] = null;
        }

        /*Get the user*/
        $data['added_by_user_name'] = $hostingManager->getTheLoggedInUser()->name;

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return Redirect::action('Domain\DomainController@view', $paginationData)->withInput()->withErrors($validator);
        }

        $data['expiry_date'] = null;
        /*Set expiry date*/
        if ($data['renewal_date'] && $data['renewal_period'] && $data['interval_id']){
            $data['expiry_date'] = $hostingManager->setTheDomainExpiryDate($data['renewal_date'], $data['renewal_period'], $data['interval_id']);
        }

        $DomainRenewalEntry = DomainRenewalEntry::create($data);

        if($DomainRenewalEntry) {

            /*Generate invoice*/
            $hostingManager->generateDomainRenewalEntryInvoice($DomainRenewalEntry);
        }

        FlashMessengerHelper::addSuccessMessage('Domain renewal entry successfully created!');

        if(isset($paginationData['new_renewal'])) {
            unset($paginationData['new_renewal']);
        }

        return Redirect::action('Domain\DomainController@view', $paginationData);

        /*END POST*/
    }

}