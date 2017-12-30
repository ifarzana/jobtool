<?php 

namespace App\Http\Controllers\Invoice;

use App\Helpers\AccHelper;
use App\Jobs\SendEmail;
use App\Managers\Editor\EditorManager;
use App\Managers\Invoice\InvoiceManager;
use App\Models\Email\EmailAccount;
use App\Models\Invoice\InvoiceEmail;
use App\Models\Invoice\InvoicePayment;
use App\Models\Invoice\InvoiceSettings;
use App\Models\Client\Client;
use App\Models\Invoice\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice\InvoiceType;
use App\Models\Settings\Settings;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Request;
use Validator;
use Config;
use Response;
use App\Models\Invoice\Invoice;
use App\Helpers\UrlHelper;
use App\Helpers\FlashMessengerHelper;

class InvoiceController extends Controller {

    /**
     * Invoice prefix
     *
     * @var string
     */
    protected $invoice_prefix = '';

    /**
     * Invoice statuses
     *
     * @var array
     */
    protected $invoice_statuses = array();

    /**
     * Invoice types
     *
     * @var array
     */
    protected $invoice_types = array();

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination_array = array(
        'paginated' => true,
        'order_by' => 'id',
        'order' => 'DESC',
        'p' => 1,
        'per_page'  => null,
        'id' => null
    );

    /**
     * Filter fields
     *
     * @var array
     */
    protected $filter_fields = array(
        'client_id',
        'from_date',
        'to_date',
        'reference',
        'sent',
        'status_id',
        'domain_id'
    );

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Invoice prefix*/
        $this->invoice_prefix = Settings::getSettings()->invoice_prefix;

        /*Invoice statuses*/
        $this->invoice_statuses = InvoiceStatus::orderBy('id', 'ASC')->pluck('name', 'id')->toArray();

        /*Invoice types*/
        $this->invoice_types = InvoiceType::orderBy('id', 'ASC')->pluck('name', 'id')->toArray();

        /*Pagination array*/
        foreach ($this->filter_fields as $field) {
            $this->pagination_array[$field] = null;
        }
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

        /*BEGIN FILTER*/
        $where = array();
        $data = array();

        $client = null;

        foreach ($this->filter_fields as $field) {
            $data[$field] = request()->get($field) ? request()->get($field) : null;
        }

        /*Format dates*/
        if ($data['from_date'] != null) {
            $data['from_date'] = date_format(date_create(Request::input('from_date')), 'Y-m-d H:i:s');
        }

        if ($data['to_date'] != null) {
            $data['to_date'] = date_format(date_create(Request::input('to_date')), 'Y-m-d H:i:s');
        }

        if(($data['client_id']) != null) {
            $where['client_id'] = $data['client_id'];

            try {
                $Client = Client::findOrFail($data['client_id']);
            }
            catch (\Exception $ex) {
                return redirect('/auth/logout');
            }

            $client = $Client->name ." (" . $Client->id .")";
        }

        if($data['reference'] != null) {
            $where['reference'] = $data['reference'];
        }

        if($data['sent'] != null) {

            if($data['sent'] == 1) {

                $where['sent'] = 0;

            }elseif($data['sent'] == 2) {

                $where['sent'] = 1;
            }
        }

        if(($data['status_id']) != null) {
            $where['status_id'] = $data['status_id'];
        }

        if($data['domain_id'] != null) {

            $Booking = Booking::where('reference', $data['domain_id'])->first();

            if($Booking != null) {
                $where['booking_id'] = $Booking->id;
            }
        }
        /*END FILTER*/

        $results = Invoice::fetchAll($paginationData, null, $where, array(), $data['from_date'], $data['to_date']);

        return view("invoice.index", array(
            'paginationData' => $paginationData,
            'results' => $results,
            'invoice_statuses' => $this->invoice_statuses,
            'client' => $client
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
            $Invoice = Invoice::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        dd('Invoice view in progress');


        /*Payments*/
        $payments = InvoicePayment::fetchAll(array('paginated' => false), null, array('invoice_id' => $Invoice->id));

        return view("invoice.view", array(
            'paginationData' => $paginationData,
            'result' => $Invoice,
            'payments' => $payments,
            'invoice_statuses' => $this->invoice_statuses,
            'invoice_types' => $this->invoice_types
        ));
    }

    /**
     * Cancel action
     *
     * @param $invoiceManager InvoiceManager
     * @param $bookingManager BookingManager
     *
     * @return object
     */
    public function cancel(InvoiceManager $invoiceManager)
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

        try{
            $Invoice =  Invoice::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        dd('Invoice cancel in progress');

        /*Get the object*/
        $object = $invoiceManager->getObjectByInvoice($Invoice->id);

        if($object == null) {
            FlashMessengerHelper::addErrorMessage('Unable to find the object ! Please contact the administrator of this system !');

            return Redirect::action('Invoice\InvoiceController@index', $paginationData);
        }

        if($Invoice->status_id == Invoice::CANCELLED_STATUS_ID) {
            return redirect('/auth/logout');
        }else {

            if($Invoice->getTotalReceived() > 0) {
                return redirect('/auth/logout');
            }

            /*Update invoice status*/
            $Invoice->status_id = Invoice::CANCELLED_STATUS_ID;
            $Invoice->update();

            /*Update booking status*/
            if($Invoice->type->is_booking_type == 1) {
                $this->updateBookingStatus($object, $bookingManager);
            }else{
                /*Update object*/
                $object->invoice_id = null;
                $object->update();
            }

            FlashMessengerHelper::addSuccessMessage('Invoice successfully cancelled !');
        }

        return Redirect::action('Invoice\InvoiceController@index', $paginationData);
    }

    /**
     * Change the booking's status to cancel
     *
     * @param $Booking Booking
     * @param $bookingManager BookingManager
     *
     * @return true
     */
    protected function updateBookingStatus($Booking, $bookingManager) {

        $OriginalBooking = $Booking->toArray();

        $Booking->status_id = Booking::CANCELLED_STATUS_ID;
        $Booking->update();

        $NewBooking = $Booking->toArray();

        $bookingManager->detectBookingChanges($OriginalBooking, $NewBooking);

        return true;
    }

    /**
     * Sent action
     *
     * @param InvoiceManager $invoiceManager
     * @return object
     */
    public function sent(InvoiceManager $invoiceManager)
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

        try{
            $Invoice =  Invoice::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the object*/
        $object = $invoiceManager->getObjectByInvoice($Invoice->id);

        if($object == null) {
            FlashMessengerHelper::addErrorMessage('Unable to find the object ! Please contact the administrator of this system !');

            return Redirect::action('Invoice\InvoiceController@index', $paginationData);
        }

        if($Invoice->sent == 1) {
            return redirect('/auth/logout');
        }

        /*No cancelled invoice*/
        if($Invoice->status_id == Invoice::CANCELLED_STATUS_ID) {
            return redirect('/auth/logout');
        }else {
            /*Update invoice status*/
            $Invoice->sent = 1;
            $Invoice->sent_at = date('Y-m-d H:i:s');
            $Invoice->email_sent = 0;

            $Invoice->update();

            FlashMessengerHelper::addSuccessMessage('Invoice successfully marked as sent !');
        }

        return Redirect::action('Invoice\InvoiceController@index', $paginationData);
    }

    /**
     * Download action
     *
     * @param InvoiceManager $invoiceManager
     * @return object
     */
    public function download(InvoiceManager $invoiceManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        $id = request()->get('id');

        try{
            $Invoice =  Invoice::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check cancelled status*/
        if($Invoice->status_id == Invoice::CANCELLED_STATUS_ID) {
            return redirect('/auth/logout');
        }

        /*Check for filename*/
        if($Invoice->filename == null) {
            return redirect('/auth/logout');
        }

        /*Get the object*/
        $object = $invoiceManager->getObjectByInvoice($Invoice->id);

        if($object == null) {
            return redirect('/auth/logout');
        }

        /*Get the function*/
        $function = (string)$Invoice->type->function;

        /*Generate and download the file*/
        $pdf = $invoiceManager->$function($object, $Invoice);

        return $pdf;
    }

    /**
     * Email action
     *
     * @param $invoiceManager InvoiceManager
     * @param $editorManager EditorManager
     * @return object
     */
    public function email(InvoiceManager $invoiceManager, EditorManager $editorManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('email') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        $id = request()->get('id');

        try {
            $Invoice = Invoice::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*Check cancelled status*/
        if($Invoice->status_id == Invoice::CANCELLED_STATUS_ID) {
            return redirect('/auth/logout');
        }

        try {
            $settings = EmailAccount::getSettings();
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        $form_data = array(
            'from' => "$settings->from_name <$settings->from_email>",
            'to' => $Invoice->client->name." <".$Invoice->client->email_address.">",
            'subject' => $Invoice->type->subject,
            'body' => $Invoice->type->body,
            'filename' => $Invoice->filename
        );

        /*BEGIN SENT EMAILS*/
        $sent_emails = InvoiceEmail::fetchAll(array('paginated' => false), null, array('invoice_id' => $Invoice->id));
        /*END SENT EMAILS*/

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = InvoiceEmail::$rules;
            $messages = InvoiceEmail::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            /*Get the logged in user*/
            $user = Auth::User();

            /*Add user details to array*/
            $data['sent_by_user_id'] = $user->id;
            $data['sent_by_user_name'] = $user->name;

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Invoice\InvoiceController@email', $paginationData)->withInput()->withErrors($validator);
            }

            /*Save invoice email*/
            $InvoiceEmail = InvoiceEmail::create(array(
                'invoice_id' => $Invoice->id,
                'to' => $data['to'],
                'subject' => $data['subject'],
                'body' => $editorManager->render($data['body'], $Invoice->client),
                'filename' => $data['filename'],
                'sent_by_user_id' => $data['sent_by_user_id'],
                'sent_by_user_name' => $data['sent_by_user_name']
            ));

            if($InvoiceEmail != null) {

                /*BEGIN SEND EMAIL*/

                /*Get the job url*/
                $job_url = $invoiceManager->getJobUrl($InvoiceEmail->id);

                /*Dispatch job*/
                dispatch(new SendEmail($job_url));

                /*END SEND EMAIL*/

                /*Update invoice*/
                $Invoice->sent = 1;
                $Invoice->sent_at = date('Y-m-d H:i:s');
                $Invoice->email_sent = 1;

                $Invoice->update();

                FlashMessengerHelper::addSuccessMessage('Email successfully sent !');

            }else{
                FlashMessengerHelper::addErrorMessage('Unable to send email !');
            }

            return Redirect::action('Invoice\InvoiceController@email', $paginationData);
        }
        /*END POST*/

        return view("invoice.email", array(
            'paginationData' => $paginationData,
            'form_data' => $form_data,
            'invoice' => $Invoice,
            'sent_emails' => $sent_emails
        ));
    }

}