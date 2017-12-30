<?php 

namespace App\Http\Controllers\Invoice;

use App\Helpers\AclManagerHelper;
use App\Helpers\CustomerAreaHelper;
use App\Http\Controllers\Controller;
use App\Managers\Invoice\InvoiceManager;
use App\Models\Booking\BookingSettings;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoicePayment;
use App\Models\PaymentMethod\PaymentMethod;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\UrlHelper;
use App\Helpers\FlashMessengerHelper;
use Redirect;
use Request;
use Validator;
use Config;
use Response;
use Auth;
use Session;
use SagePay;
use Route;

class InvoicePaymentController extends Controller {

    /**
     * Payment methods
     *
     * @var array
     */
    protected $payment_methods = array();

    /**
     * Non gateway payment methods
     *
     * @var array
     */
    protected $non_gateway_payment_methods = array();

    /**
     * Booking settings
     *
     * @var object|null
     */
    protected $booking_settings = null;

    /*Schedule redirect ask key*/
    const SCHEDULE_REDIRECT_ASK_KEY = 'schedule_redirect_ask';

    /**
     * Super administrator flag
     *
     * @var bool
     */
    protected $super_administrator = false;

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
        'id' => null,
        'per_page'  => null,
        'invoice_id' => null,
        'payment_method_id' => null,
        'redirect_booking_id' => null,
        'status' => null,
    );

    /**
     * Filter fields
     *
     * @var array
     */
    protected $filter_fields = array(
        'customer_id',
        'from_date',
        'to_date',
        'reference',
        'sent',
        'status_id'
    );

    /**
     * Construct - set all arrays and objects
     *
     * @return void|object
     */
    public function __construct()
    {
        /*Payment methods*/
        $this->payment_methods = PaymentMethod::orderBy('name', 'ASC')->pluck('name', 'id')->toArray();

        /*Non gateway payment methods*/
        $this->non_gateway_payment_methods = PaymentMethod::where('sagepay', 0)->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();

        /*Booking invoice*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Pagination array*/
        foreach ($this->filter_fields as $field) {
            $this->pagination_array[$field] = null;
        }

        /*Booking settings*/
        $this->booking_settings = BookingSettings::getSettings();

        /*Super administrator*/
        $this->super_administrator = Auth::user()->isSuperAdministrator();
    }

    /**
     * Index action
     *
     * @return object
     */
    public function index()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $results = InvoicePayment::fetchAll(array('paginated' => false), null, array('invoice_id' => $Invoice->id));

        return view("invoice.payment.index", array(
            'paginationData' => $paginationData,
            'results' => $results,
            'invoice' => $Invoice,
            'payment_methods' => $this->payment_methods,
            'schedule_redirect_ask_key' => self::SCHEDULE_REDIRECT_ASK_KEY,
            'super_administrator' => $this->super_administrator
        ));
    }


    /**
     * Payment method select action
     *
     * @return object
     */
    public function paymentMethodSelect()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        if(Request::isMethod('post')) {

            $data = Request::input();

            /*Payment method*/
            $payment_method_id = (int)$data['payment_method_id'];

            try {
                $PaymentMethod = PaymentMethod::findOrFail($payment_method_id);
            }
            catch (\Exception $ex) {
                return redirect('/auth/logout');
            }

            /*Redirect booking id*/
            $redirect_booking_id = null;

            if(array_key_exists('redirect_booking_id', $data)) {
                $paginationData['redirect_booking_id'] = $data['redirect_booking_id'];
            }

            $paginationData['payment_method_id'] = $payment_method_id;

            /*Payment type*/
            $payment_type = $data['payment_type'];

            if($payment_type == 'refund') {

                return Redirect::action('Invoice\InvoicePaymentController@refund', $paginationData);

            }else if($payment_type == 'payment') {

                if($PaymentMethod->sagepay == 1) {
                    return Redirect::action('Invoice\InvoicePaymentController@createSagepay', $paginationData);
                }else{
                    return Redirect::action('Invoice\InvoicePaymentController@create', $paginationData);
                }

            }

        }

        /*Return to index if request is not post*/
        return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);

    }

    /**
     * Create action
     *
     * @param  $invoiceManager InvoiceManager
     * @return object
     */
    public function create(InvoiceManager $invoiceManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN INVOICE*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END INVOICE*/

        /*Check if can accept payments*/
        if($Invoice->canAcceptPayments() == false) {
            return redirect('/auth/logout');
        }

        /*BEGIN PAYMENT METHOD*/
        $payment_method_id = request()->get('payment_method_id');

        try {
            $PaymentMethod = PaymentMethod::findOrFail($payment_method_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END PAYMENT METHOD*/

        /*Check payment method*/
        if($PaymentMethod->sagepay != 0) {
            return redirect('/auth/logout');
        }

        /*Amounts to pay*/
        $amounts_to_pay = $invoiceManager->getAmountsToPay($Invoice);

        $can_pay_max = $Invoice->outstanding();

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = InvoicePayment::$rules;
            $messages = InvoicePayment::$messages;
            
            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            /*Add created by*/
            $user = Auth::User();

            $data['added_by_user_id'] = $user->id;
            $data['added_by_user_name'] = $user->name;

            /*Check value*/

            if( ($data['amount_select'] == 'custom') AND ($amounts_to_pay['custom'] == true)) {
                $data['value'] = $data['custom_value'];
            }else{
                $data['value'] = explode('-', $data['amount_select'])[0];
            }

            $rules['value'].= '|max:'.$can_pay_max;

            if($amounts_to_pay['minimum_deposit']['enabled'] == true) {

                $rules['value'] = str_replace('|min:0.01', '|min:'.$amounts_to_pay['minimum_deposit']['amount'], $rules['value']);
            }

            /*Check created at*/
            if(AclManagerHelper::hasPermission('payment-date')) {
                $rules['created_at'].= '|required';

                /*Format created at*/
                if(array_key_exists('created_at', $data)) {
                    $data['created_at'] = date_format(date_create($data['created_at']), 'Y-m-d H:i:s');

                    if(strtotime($data['created_at']) > strtotime(date('Y-m-d H:i:s'))) {
                        $rules['created_at'].= '|alpha';
                    }

                }

            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Invoice\InvoicePaymentController@create', $paginationData)->withInput()->withErrors($validator);
            }

            $data['value'] = str_replace(',', '', $data['value']);

            InvoicePayment::create($data);

            /*BEGIN UPDATE INVOICE*/
            $invoiceManager->updateInvoiceOnPayment($Invoice->id);
            /*END UPDATE INVOICE*/

            /*BEGIN UPDATE BOOKING*/
            $deposit_paid = $invoiceManager->updateBookingOnPayment($Invoice->id);
            /*END UPDATE BOOKING*/

            FlashMessengerHelper::addSuccessMessage('Invoice payment successfully created!');

            /*BEGIN REDIRECT TO SCHEDULE*/
            if( ($deposit_paid === true) AND ((AclManagerHelper::hasPermission('read', 'bookings'))) ) {

                if( $this->booking_settings->schedule_redirect_after_deposit_paid == 1 ) {

                    return Redirect::action('Booking\BookingController@scheduleScrollToBooking', array('id' => $Invoice->booking_id));

                }elseif ($this->booking_settings->schedule_redirect_after_deposit_paid == 2) {

                    Session::put(self::SCHEDULE_REDIRECT_ASK_KEY, true);

                    return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);

                }
            }
            /*END REDIRECT TO SCHEDULE*/

            if(array_key_exists('redirect_booking_id', $paginationData)) {
                if( ($paginationData['redirect_booking_id'] != null) AND (AclManagerHelper::hasPermission('update', 'bookings')) ) {
                    return Redirect::action('Booking\BookingController@edit', array('id' => $paginationData['redirect_booking_id']));
                }
            }

            return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);
        }
        /*END POST*/

        return view("invoice.payment.create", array(
            'paginationData' => $paginationData,
            'invoice' => $Invoice,
            'PaymentMethod' => $PaymentMethod,
            'amounts_to_pay' => $amounts_to_pay
        ));
    }

    /**
     * Create sagepay action
     *
     * @param  $invoiceManager InvoiceManager
     * @return object
     */
    public function createSagepay(InvoiceManager $invoiceManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN INVOICE*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if can accept payments*/
        if($Invoice->canAcceptPayments() == false) {
            return redirect('/auth/logout');
        }

        /*END INVOICE*/

        /*BEGIN PAYMENT METHOD*/
        $payment_method_id = request()->get('payment_method_id');

        try {
            $PaymentMethod = PaymentMethod::findOrFail($payment_method_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END PAYMENT METHOD*/

        /*Check payment method*/
        if($PaymentMethod->sagepay != 1) {
            return redirect('/auth/logout');
        }

        /*Customer alert*/
        $customer_alert = CustomerAreaHelper::getCustomerAlert($Invoice->customer, false);

        /*Amounts to pay*/
        $amounts_to_pay = $invoiceManager->getAmountsToPay($Invoice);

        $can_pay_max = $Invoice->outstanding();

        /*Encrypted code*/
        $encrypted_code = null;

        /*Proceed to payment*/
        $proceed_to_payment = false;

        /*SagePay object*/
        $object = null;

        /*Error status*/
        $error_status = null;

        /*Error message*/
        $error_message = null;

        /*Add created by*/
        $user = Auth::User();

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = InvoicePayment::$rules;
            $messages = InvoicePayment::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            /*Check customer alert*/
            if($customer_alert['display'] == true) {
                return redirect('/auth/logout');
            }

            $data['added_by_user_id'] = $user->id;
            $data['added_by_user_name'] = $user->name;

            /*Check value*/
            if( ($data['amount_select'] == 'custom') AND ($amounts_to_pay['custom'] == true)) {
                $data['value'] = $data['custom_value'];
            }else{
                $data['value'] = explode('-', $data['amount_select'])[0];
            }

            $rules['value'].= '|max:'.$can_pay_max;

            if($amounts_to_pay['minimum_deposit']['enabled'] == true) {

                $rules['value'] = str_replace('|min:0.01', '|min:'.$amounts_to_pay['minimum_deposit']['amount'], $rules['value']);
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Invoice\InvoicePaymentController@createSagepay', $paginationData)->withInput()->withErrors($validator);
            }

            $proceed_to_payment = true;

            /*BEGIN SAGEPAY DETAILS*/
            $routeDetails = UrlHelper::getRouteDetails();
            $controller = $routeDetails['controller'];
            $action = $routeDetails['action'];

            /*Set SagePay data*/
            $this->setSagePayData($controller, $action, $paginationData, $PaymentMethod, $Invoice, $data);

            $encrypted_code = SagePay::getCrypt();
            /*END SAGEPAY DETAILS*/

            /*Get the SagePay object*/
            $object = SagePay::getObject();
        }
        /*END POST*/

        /*BEGIN RESPONSE FROM SAGEPAY*/
        $crypt = request()->get('crypt');

        if($crypt != null) {

            /*Set encrypt password*/
            SagePay::setEncryptPassword(Crypt::decrypt($PaymentMethod->encrypt_password));

            $response = SagePay::decode($crypt);

            if($response['Status'] == 'OK') {

                $payment_data = array(
                    'invoice_id' => $Invoice->id,
                    'value' => str_replace(',', '', $response['Amount']),
                    'payment_method_id' => $PaymentMethod->id,
                    'refund' => 0,
                    'added_by_user_id' => $user->id,
                    'added_by_user_name' => $user->name,
                    'transaction_details' => json_encode($response)
                );

                InvoicePayment::create($payment_data);

                /*BEGIN UPDATE INVOICE*/
                $invoiceManager->updateInvoiceOnPayment($Invoice->id);
                /*END UPDATE INVOICE*/

                /*BEGIN UPDATE BOOKING*/
                $deposit_paid = $invoiceManager->updateBookingOnPayment($Invoice->id);
                /*END UPDATE BOOKING*/

                FlashMessengerHelper::addSuccessMessage('Invoice payment successfully created!');

                /*BEGIN REDIRECT TO SCHEDULE*/
                if( ($deposit_paid === true) AND ((AclManagerHelper::hasPermission('read', 'bookings'))) ) {

                    if( $this->booking_settings->schedule_redirect_after_deposit_paid == 1 ) {

                        return Redirect::action('Booking\BookingController@scheduleScrollToBooking', array('id' => $Invoice->booking_id));

                    }elseif ($this->booking_settings->schedule_redirect_after_deposit_paid == 2) {

                        Session::put(self::SCHEDULE_REDIRECT_ASK_KEY, true);

                        return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);

                    }
                }
                /*END REDIRECT TO SCHEDULE*/

                if(array_key_exists('redirect_booking_id', $paginationData)) {
                    if( ($paginationData['redirect_booking_id'] != null) AND (AclManagerHelper::hasPermission('update', 'bookings')) ) {
                        return Redirect::action('Booking\BookingController@edit', array('id' => $paginationData['redirect_booking_id']));
                    }
                }

                return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);

            }else{
                $error_status = $response['Status'];
                $error_message = $response['StatusDetail'];
            }

        }
        /*END RESPONSE FROM SAGEPAY*/

        return view("invoice.payment.create-sagepay", array(
            'paginationData' => $paginationData,
            'invoice' => $Invoice,
            'encrypted_code' => $encrypted_code,
            'PaymentMethod' => $PaymentMethod,
            'proceed_to_payment' => $proceed_to_payment,
            'object' => $object,
            'error_status' => $error_status,
            'error_message' => $error_message,
            'customer_alert' => $customer_alert,
            'amounts_to_pay' => $amounts_to_pay
        ));
    }

    /**
     * Set SagePay data
     *
     * @param $controller string
     * @param $action string
     * @param $paginationData array
     * @param $PaymentMethod PaymentMethod
     * @param $Invoice Invoice
     * @param $data array
     *
     * @return true
     */
    protected function setSagePayData($controller, $action, $paginationData, $PaymentMethod, $Invoice, $data)
    {
        /*Get the customer*/
        $Customer = $Invoice->customer;

        /*Set encrypt password*/
        SagePay::setEncryptPassword(Crypt::decrypt($PaymentMethod->encrypt_password));

        /*Apply 3d secure*/
        SagePay::setApply3DSecure($PaymentMethod->apply_3d_secure);

        /*Allow gift aid*/
        SagePay::setAllowGiftAid($PaymentMethod->allow_gift_aid);

        /*Apply AVSCV2*/
        SagePay::setApplyAVSCV2($PaymentMethod->apply_avscv2);

        /*Set amount*/
        SagePay::setAmount($data['value']);

        /*Set currency*/
        SagePay::setCurrency($Invoice->currency->name);

        /*Set description*/
        $description = 'Invoice ref. #'.$Invoice->reference." - ".$Invoice->type->name;

        if($Invoice->type->is_booking_type == 1) {
            $description = 'Booking ref. #'.$Invoice->booking->reference;
        }

        SagePay::setDescription($description);

        /*Vendor email address*/
        SagePay::setVendorEMail($PaymentMethod->vendor_email_address);

        /*Send email*/
        SagePay::setSendEMail($PaymentMethod->send_email);

        /*Customer name*/
        SagePay::setCustomerName($Customer->first_name." ".$Customer->last_name);

        /*Customer email*/
        if(!empty($Customer->email_address)) {
            SagePay::setCustomerEMail($Customer->email_address);
        }

        /*Customer first name*/
        SagePay::setBillingFirstnames($Customer->first_name);

        /*Customer last name*/
        SagePay::setBillingSurname($Customer->last_name);

        /*Customer town*/
        if(!empty($Customer->town)) {
            SagePay::setBillingCity($Customer->town);
        }elseif(!empty($Customer->county)){
            SagePay::setBillingCity($Customer->county);
        }else{
            SagePay::setBillingCity($Customer->address_line_2);
        }

        /*Customer postcode*/
        SagePay::setBillingPostCode($Customer->postcode);

        /*House number and street*/
        SagePay::setBillingAddress1($Customer->address_line_1);

        /*Town*/
        SagePay::setBillingAddress2($Customer->address_line_2);

        /*Country*/
        SagePay::setBillingCountry($Customer->country->code);

        /*Phone*/
        if(!empty($Customer->mobile_phone_number)) {
            SagePay::setBillingPhone($Customer->mobile_phone_number);
        }else if(!empty($Customer->home_phone_number)) {
            SagePay::setBillingPhone($Customer->home_phone_number);
        }

        /*Copy details to delivery*/
        SagePay::setDeliverySameAsBilling();

        SagePay::setSuccessURL(UrlHelper::getUrl($controller, $action, $paginationData, array('status' => 'success')));
        SagePay::setFailureURL(UrlHelper::getUrl($controller, $action, $paginationData, array('status' > 'fail')));

        return true;
    }

    /**
     * Refund action
     *
     * @param $invoiceManager InvoiceManager
     * @return object
     */
    public function refund(InvoiceManager $invoiceManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN INVOICE*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Check if can accept refunds*/
        if($Invoice->canAcceptRefunds() == false) {
            return redirect('/auth/logout');
        }

        /*END INVOICE*/

        $can_refund_max_amount = $Invoice->getCanRefundMaxAmount();

        /*BEGIN PAYMENT METHOD*/
        $payment_method_id = request()->get('payment_method_id');

        try {
            $PaymentMethod = PaymentMethod::findOrFail($payment_method_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END PAYMENT METHOD*/

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = InvoicePayment::$rules;
            $messages = InvoicePayment::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            /*Add created by*/
            $user = Auth::User();

            $data['added_by_user_id'] = $user->id;
            $data['added_by_user_name'] = $user->name;

            /*Check value*/
            $rules['value'].= '|max:'.$can_refund_max_amount;

            $messages['value.max'] = 'The refunded amount may not be greater than '.$Invoice->currency->symbol.sprintf('%0.2f', $can_refund_max_amount);

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Invoice\InvoicePaymentController@refund', $paginationData)->withInput()->withErrors($validator);
            }

            $data['value'] = str_replace(',', '', $data['value']);

            InvoicePayment::create($data);

            /*BEGIN UPDATE INVOICE*/
            $invoiceManager->updateInvoiceOnRefund($Invoice->id);
            /*END UPDATE INVOICE*/

            /*BEGIN BOOKING ON REFUND*/
            $invoiceManager->updateBookingOnRefund($Invoice->id);
            /*BEGIN BOOKING ON REFUND*/

            FlashMessengerHelper::addSuccessMessage('Invoice refund successfully created!');

            return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);
        }
        /*END POST*/

        return view("invoice.payment.refund", array(
            'paginationData' => $paginationData,
            'invoice' => $Invoice,
            'PaymentMethod' => $PaymentMethod
        ));
    }

    /**
     * Online refunded action
     *
     * @return object
     */
    public function refundedOnline()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN INVOICE PAYMENT*/
        $id = request()->get('id');

        try {
            $InvoicePayment = InvoicePayment::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END INVOICE PAYMENT*/

        /*BEGIN INVOICE*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END INVOICE*/

        /*Check payment*/
        if($InvoicePayment->refund == 0) {
            return redirect('/auth/logout');
        }

        if($InvoicePayment->refunded_online == 1) {
            return redirect('/auth/logout');
        }

        if($InvoicePayment->paymentMethod->sagepay == 0) {
            return redirect('/auth/logout');
        }

        /*Update*/
        $InvoicePayment->refunded_online = 1;
        $InvoicePayment->update();

        FlashMessengerHelper::addSuccessMessage('Invoice payment successfully marked as refunded online !');

        return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);
    }

    /**
     * Edit action
     *
     * @return object
     */
    public function edit()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment-update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN INVOICE PAYMENT*/
        $id = request()->get('id');

        try {
            $InvoicePayment = InvoicePayment::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END INVOICE PAYMENT*/

        /*BEGIN INVOICE*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END INVOICE*/

        /*Check sagepay*/
        if($InvoicePayment->paymentMethod->sagepay == 1) {
            return redirect('/auth/logout');
        }

        /*Check invoice status*/
        if($Invoice->status_id == Invoice::CANCELLED_STATUS_ID) {
            return redirect('/auth/logout');
        }

        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = InvoicePayment::$rules;
            $messages = InvoicePayment::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            /*Check created at*/
            if(AclManagerHelper::hasPermission('payment-date')) {
                $rules['created_at'].= '|required';

                /*Format created at*/
                if(array_key_exists('created_at', $data)) {
                    $data['created_at'] = date_format(date_create($data['created_at']), 'Y-m-d H:i:s');

                    if(strtotime($data['created_at']) > strtotime(date('Y-m-d H:i:s'))) {
                        $rules['created_at'].= '|alpha';
                    }
                }

            }

            /*Add the missing data*/
            $data['refund'] = $InvoicePayment->refund;
            $data['added_by_user_id'] = $InvoicePayment->added_by_user_id;
            $data['added_by_user_name'] = $InvoicePayment->added_by_user_name;

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Invoice\InvoicePaymentController@edit', $paginationData)->withInput()->withErrors($validator);
            }

            $InvoicePayment->update($data);

            FlashMessengerHelper::addSuccessMessage('Payment successfully updated !');

            return Redirect::action('Invoice\InvoicePaymentController@edit', $paginationData);
        }
        /*END POST*/

        return view("invoice.payment.edit", array(
            'paginationData' => $paginationData,
            'result' => $InvoicePayment,
            'invoice' => $Invoice,
            'non_gateway_payment_methods' => $this->non_gateway_payment_methods
        ));
    }

    /**
     * Delete action
     *
     * @param $invoiceManager InvoiceManager
     * @return object
     */
    public function delete(InvoiceManager $invoiceManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('payment') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        /*BEGIN CHECK SUPER-ADMINISTRATOR*/
        if($this->super_administrator == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK SUPER-ADMINISTRATOR*/

        /*BEGIN INVOICE PAYMENT*/
        $id = request()->get('id');

        try {
            $InvoicePayment = InvoicePayment::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }
        /*END INVOICE PAYMENT*/

        /*BEGIN INVOICE*/
        $invoice_id = request()->get('invoice_id');

        try {
            $Invoice = Invoice::findOrFail($invoice_id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*END INVOICE*/

        /*Check sagepay*/
        if($InvoicePayment->paymentMethod->sagepay == 1) {
            return redirect('/auth/logout');
        }

        /*Check invoice status*/
        if($Invoice->status_id == Invoice::CANCELLED_STATUS_ID) {
            return redirect('/auth/logout');
        }

        try {
            $InvoicePayment->delete();

            FlashMessengerHelper::addSuccessMessage('Payment successfully deleted !');
        }
        catch (\Exception $ex) {

            FlashMessengerHelper::addErrorMessage('Cannot be deleted because item is in use !');
        }

        $invoiceManager->updateInvoiceOnDeletePayment($Invoice->id);

        return Redirect::action('Invoice\InvoicePaymentController@index', $paginationData);
    }

}