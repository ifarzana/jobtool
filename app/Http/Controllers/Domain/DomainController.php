<?php

namespace App\Http\Controllers\Domain;

use App\Http\Controllers\Controller;
use App\Managers\Domain\DomainManager;
use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Currency\Currency;
use App\Models\Domain\AssignedDomain;
use App\Models\Domain\Domain;
use App\Models\Domain\DomainRenewalEntry;
use App\Models\Domain\Interval;
use App\Models\Domain\Registrar;
use App\Models\Domain\Server;
use App\Models\Invoice\Invoice;
use Redirect;
use Request;
use Response;
use Config;
use Validator;
use Auth;
use App\Helpers\UrlHelper;
use App\Helpers\FlashMessengerHelper;

class DomainController extends Controller {

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

        $results = Domain::fetchAll($paginationData, request()->get('search_by'), $array);

        return view("domain.index", array(
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

        /*Get the domain*/
        $id = request()->get('id');

        try {
            $domain = Domain::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the contact*/
        $ClientContact = ClientContact::find($domain->contact_id);

        $contact = null;
        $no_email_address = false;

        if($ClientContact){
            if (empty($ClientContact->email_address)){
                $no_email_address = true;
            }

            $contact = $ClientContact->name.' ('.( (!empty($ClientContact->email_address) ) ? $ClientContact->email_address :'no email address').')';
        }

        /*Get the invoice*/
        $Invoice = Invoice::find($domain->invoice_id);

        /*Format dates*/
        if(!empty($domain->domain_renewal_date)) {
            $domain->domain_renewal_date = date_format(date_create($domain->domain_renewal_date), 'd-M-Y');
        }

        if(!empty($domain->registration_date)) {
            $domain->registration_date = date_format(date_create($domain->registration_date), 'd-M-Y');
        }

        if(!empty($domain->expiry_date)) {
            $domain->expiry_date = date_format(date_create($domain->expiry_date), 'd-M-Y');
        }

        if(!empty($domain->next_due_date)) {
            $domain->next_due_date = date_format(date_create($domain->next_due_date), 'd-M-Y');
        }

        /*Registrars*/
        $registrars = Registrar::lists('name', 'id')->toArray();

        /*Servers*/
        $servers = Server::lists('name', 'id')->toArray();

        /*Intervals*/
        $intervals = Interval::lists('name', 'id')->toArray();

        /*Get all the renewal entries*/
        $renewal_entries = DomainRenewalEntry::where('domain_id', $domain->id)->get();

        /*Due date*/
        $due_date = $domain->expiry_date;

        return view("domain.view", array(
            'paginationData' => $paginationData,
            'result' => $domain,
            'contact' => $contact,
            'Invoice' => $Invoice,
            'no_email_address' => $no_email_address,
            'registrars' => $registrars,
            'servers' => $servers,
            'renewal_entries' => $renewal_entries,
            'intervals' => $intervals,
            'due_date' => $due_date,

        ));
    }

    /**
     * View action
     *
     * @return object
     */
    public function altView()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*Get the domain*/
        $id = request()->get('id');

        try {
            $domain = Domain::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the contact*/
        $ClientContact = ClientContact::find($domain->contact_id);

        $contact = null;
        $no_email_address = false;

        if($ClientContact){
            if (empty($ClientContact->email_address)){
                $no_email_address = true;
            }

            $contact = $ClientContact->name.' ('.( (!empty($ClientContact->email_address) ) ? $ClientContact->email_address :'no email address').')';
        }

        /*Get the invoice*/
        $Invoice = Invoice::find($domain->invoice_id);

        /*Format dates*/
        if(!empty($domain->domain_renewal_date)) {
            $domain->domain_renewal_date = date_format(date_create($domain->domain_renewal_date), 'd-M-Y');
        }

        if(!empty($domain->registration_date)) {
            $domain->registration_date = date_format(date_create($domain->registration_date), 'd-M-Y');
        }

        if(!empty($domain->expiry_date)) {
            $domain->expiry_date = date_format(date_create($domain->expiry_date), 'd-M-Y');
        }

        if(!empty($domain->next_due_date)) {
            $domain->next_due_date = date_format(date_create($domain->next_due_date), 'd-M-Y');
        }

        /*Registrars*/
        $registrars = Registrar::lists('name', 'id')->toArray();

        /*Servers*/
        $servers = Server::lists('name', 'id')->toArray();

        /*Intervals*/
        $intervals = Interval::lists('name', 'id')->toArray();

        /*Get all the renewal entries*/
        $renewal_entries = DomainRenewalEntry::where('domain_id', $domain->id)->get();

        /*Due date*/
        $due_date = $domain->expiry_date;

        return view("domain.alt-view", array(
            'paginationData' => $paginationData,
            'result' => $domain,
            'contact' => $contact,
            'Invoice' => $Invoice,
            'no_email_address' => $no_email_address,
            'registrars' => $registrars,
            'servers' => $servers,
            'renewal_entries' => $renewal_entries,
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
            $domain = Domain::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        dd('edit the domain');

        return view("domain.renew", array(
            'paginationData' => $paginationData,
            'result' => $domain,
        ));
    }

    /**
     * Create action
     *
     * @param $domainManager DomainManager
     * @return object
     */
    public function create(DomainManager $domainManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('create') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*Registrars*/
        $registrars = Registrar::lists('name', 'id')->toArray();

        /*Servers*/
        $servers = Server::lists('name', 'id')->toArray();

        /*Intervals*/
        $intervals = Interval::lists('name', 'id')->toArray();

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = Domain::$rules;
            $messages = Domain::$messages;

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
                return Redirect::action('Domain\DomainController@create', $paginationData)->withInput()->withErrors($validator);
            }

            /*BEGIN SET EXPIRY DATE*/
            $data['expiry_date'] = null;
            if ($data['registration_date'] && $data['registration_period'] && $data['interval_id']) {
                $data['expiry_date'] = $domainManager->setTheDomainExpiryDate($data['registration_date'], $data['registration_period'], $data['interval_id']);
            }
            /*END SET EXPIRY DATE*/

            $Domain = Domain::create($data);

            if($Domain) {

                /*Creates a domain invoice and updates the domain with the new invoice*/
                $domainManager->generateInvoice($Domain);

                /*Assign domain to a user*/
                $domainManager->assignDomainToUser($Domain);

                /*Dispatch a job when a domain is assigned to an user*/
                $domainManager->createJob($Domain);

            }

            FlashMessengerHelper::addSuccessMessage('Domain successfully created!');

            return Redirect::action('Domain\DomainController@index', $paginationData);
        }
        /*END POST*/

        return view("domain.create", array(
            'paginationData' => $paginationData,
            'registrars' => $registrars,
            'servers' => $servers,
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
            $domain = Domain::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Domain invoice id*/
        $invoice_id = $domain->invoice_id;

        /*Renewal entries*/
        $renewal_entries = DomainRenewalEntry::where('domain_id', $domain->id)->get();

        /*Assigned domains*/
        $assigned_domains = AssignedDomain::where('domain_id', $domain->id)->get();

        /*Invoices*/
        $invoice = Invoice::findOrFail($invoice_id);



        try {

            /*If there are renewal entries you cannot delete the domain*/
            if ( empty (count($renewal_entries) )  AND ($invoice->status_id == 2)) {

                foreach ($assigned_domains as $assigned_domain){
                    $assigned_domain->delete();
                }

                $domain->delete();

                /*If there is an invoice then delete it*/
                $invoice->delete();

                FlashMessengerHelper::addSuccessMessage('Domain successfully deleted !');
            }
            else {

                FlashMessengerHelper::addErrorMessage('Cannot be deleted because item is in use !');
            }


        }
        catch (\Exception $ex) {

            FlashMessengerHelper::addErrorMessage('Cannot be deleted because item is in use !');
        }

        return Redirect::action('Domain\DomainController@index', $paginationData);
    }

    /**
     * Create Renewal Entry action
     *
     * @param $domainManager DomainManager
     * @return object
     */
    public function createRenewalEntry(DomainManager $domainManager)
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
        $data['added_by_user_name'] = $domainManager->getTheLoggedInUser()->name;

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return Redirect::action('Domain\DomainController@view', $paginationData)->withInput()->withErrors($validator);
        }

        $data['expiry_date'] = null;
        /*Set expiry date*/
        if ($data['renewal_date'] && $data['renewal_period'] && $data['interval_id']){
            $data['expiry_date'] = $domainManager->setTheDomainExpiryDate($data['renewal_date'], $data['renewal_period'], $data['interval_id']);
        }

        $domain_id = request()->get('domain_id');

        try {
            $domain = Domain::findOrFail($domain_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        $DomainRenewalEntry = DomainRenewalEntry::create($data);

        if($DomainRenewalEntry) {

            /*Update expiry_date*/
            $domain->expiry_date = $data['expiry_date'];
            $domain->save();

            /*Generate invoice*/
            $domainManager->generateDomainRenewalEntryInvoice($DomainRenewalEntry);
        }

        FlashMessengerHelper::addSuccessMessage('Domain renewal entry successfully created!');

        if(isset($paginationData['new_renewal'])) {
            unset($paginationData['new_renewal']);
        }

        return Redirect::action('Domain\DomainController@view', $paginationData);

        /*END POST*/
    }

}