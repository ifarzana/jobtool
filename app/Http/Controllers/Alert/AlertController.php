<?php 

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Managers\Alert\AlertManager;
use App\Models\Invoice\Invoice;
use Redirect;
use Request;
use Validator;
use App\Helpers\FlashMessengerHelper;

class AlertController extends Controller {

    /**
     * Booking manager
     *
     * @var object
     */
    protected $bookingManager;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Booking manager*/
        //$this->bookingManager = $bookingManager;
    }

    /**
     * Index action
     *
     * @param $alertManager AlertManager
     * @return object
     */
    public function index(AlertManager $alertManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        $key = request()->get('key');

        $array = $alertManager->getResultsByKey($key);

        //dd($array);
        
        return view("alert.templates.".$key, array(
            'results' => $array['results'],
            'Alert' => $array['alert']
        ));
    }

    /**
     * Update
     *
     * @return object
     */
    public function update()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('update') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        $id = request()->get('id');
        $key = request()->get('key');
        $function = request()->get('function');

        $message = $this->$function($id);

        FlashMessengerHelper::addSuccessMessage($message);

        return Redirect::action('Alert\AlertController@index', array('key' => $key));
    }

    /***********************************************************************************************CUSTOMERS**********************************************************************************************/

    /**
     * Update brochure request customer
     *
     * @param $id int
     * @return object|string
     */
    protected function updateBrochureRequestCustomer($id)
    {
        try {
            $Customer = Customer::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        $Customer->brochure_request = 0;
        $Customer->update();

        $message = 'Customer successfully updated !';

        return $message;
    }

    /***********************************************************************************************BOOKINGS**********************************************************************************************/

    /**
     * Update booking arrived
     *
     * @param $id int
     * @return object|string
     */
    protected function updateBookingArrived($id)
    {
        try {
            $BookingItem = BookingItem::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the original object*/
        $OriginalBookingItem = $BookingItem->toArray();

        $BookingItem->arrived = 1;
        $BookingItem->update();

        /*Get the new object*/
        $NewBookingItem = BookingItem::find($BookingItem->id);

        /*Detect changes*/
        $this->bookingManager->detectBookingItemChanges($OriginalBookingItem, $NewBookingItem->toArray(), 0, 0);

        $message = 'Booking successfully updated !';

        return $message;
    }

    /**
     * Update booking fob issued
     *
     * @param $id int
     * @return object|string
     */
    protected function updateBookingFobIssued($id)
    {
        try {
            $BookingItem = BookingItem::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the original object*/
        $OriginalBookingItem = $BookingItem->toArray();

        $BookingItem->fob_issued = 1;
        $BookingItem->fob_issued_at = date('Y-m-d H:i:s');
        $BookingItem->update();

        /*Get the new object*/
        $NewBookingItem = BookingItem::find($BookingItem->id);

        /*Detect changes*/
        $this->bookingManager->detectBookingItemChanges($OriginalBookingItem, $NewBookingItem->toArray(), 0, 0);

        $message = 'Booking successfully updated !';

        return $message;
    }

    /**
     * Update booking departed
     *
     * @param $id int
     * @return object|string
     */
    protected function updateBookingDeparted($id)
    {
        try {
            $BookingItem = BookingItem::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        if($BookingItem->arrived == 0) {
            return redirect('/auth/logout');
        }

        /*Get the original object*/
        $OriginalBookingItem = $BookingItem->toArray();

        $BookingItem->departed = 1;
        $BookingItem->update();

        /*Get the new object*/
        $NewBookingItem = BookingItem::find($BookingItem->id);

        /*Detect changes*/
        $this->bookingManager->detectBookingItemChanges($OriginalBookingItem, $NewBookingItem->toArray(), 0, 0);

        $message = 'Booking successfully updated !';

        return $message;
    }

    /**
     * Update booking fob returned
     *
     * @param $id int
     * @return object|string
     */
    protected function updateBookingFobReturned($id)
    {
        try {
            $BookingItem = BookingItem::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        if($BookingItem->fob_issued == 0) {
            return redirect('/auth/logout');
        }

        /*Get the original object*/
        $OriginalBookingItem = $BookingItem->toArray();

        $BookingItem->fob_returned = 1;
        $BookingItem->fob_returned_at = date('Y-m-d H:i:s');
        $BookingItem->update();

        /*Get the new object*/
        $NewBookingItem = BookingItem::find($BookingItem->id);

        /*Detect changes*/
        $this->bookingManager->detectBookingItemChanges($OriginalBookingItem, $NewBookingItem->toArray(), 0, 0);

        $message = 'Booking successfully updated !';

        return $message;
    }


    /**
     * Update cleaned booking
     *
     * @param $id int
     * @return object|string
     */
    protected function updateCleanedBooking($id)
    {
        try {
            $BookingItem = BookingItem::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        if($BookingItem->cleaned == 1) {
            return redirect('/auth/logout');
        }

        /*Get the original object*/
        $OriginalBookingItem = $BookingItem->toArray();

        $BookingItem->cleaned = 1;
        $BookingItem->update();

        /*Get the new object*/
        $NewBookingItem = BookingItem::find($BookingItem->id);

        /*Detect changes*/
        $this->bookingManager->detectBookingItemChanges($OriginalBookingItem, $NewBookingItem->toArray(), 0, 0);

        $message = 'Booking successfully updated !';

        return $message;
    }

    /***********************************************************************************************INVOICES**********************************************************************************************/

    /**
     * Update unsent invoice
     *
     * @param $id int
     * @return object|string
     */
    protected function updateUnsentInvoice($id)
    {
        try {
            $Invoice = Invoice::findOrFail($id);
        }
        catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        if($Invoice->sent == 0) {
            return redirect('/auth/logout');
        }

        $Invoice->sent = 1;
        $Invoice->sent_at = date('Y-m-d H:i:s');
        $Invoice->email_sent = 0;
        $Invoice->update();

        $message = 'Invoice successfully updated !';

        return $message;
    }

}