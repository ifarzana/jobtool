<?php 

namespace App\Http\Controllers\Configuration\Email;

use App\Http\Controllers\Controller;
use App\Managers\Email\EmailManager;
use App\Models\Email\EmailAccount;
use Redirect;
use Request;
use Validator;
use Config;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\UrlHelper;
use App\Helpers\FlashMessengerHelper;

class EmailAccountController extends Controller {

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination_array = array(
        'paginated' => true,
        'order_by'  => 'type',
        'order'     => 'ASC',
        'p'         => 1,
        'id'        => null,
        'per_page'  => null,
        'search_by' => null
    );

    /**
     * Encryption types
     *
     * @var array
     */
    protected $encryption_types;

    /**
     * Email types
     *
     * @var array
     */
    protected $types = array(
        'main' => 'Main'
    );

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Encryption types*/
        $this->encryption_types = Config::get('mail')['encryption_types'];
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

        $results = EmailAccount::fetchAll($paginationData, request()->get('search_by'), array());

        return view("configuration.email.email-account.index", array(
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
        if ($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        try {
            $settings = EmailAccount::findOrFail($id);
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Decrypt password*/
        if(!empty($settings->password)) {
            $settings->password = Crypt::decrypt($settings->password);
        }

        return view("configuration.email.email-account.view", array(
            'paginationData' => $paginationData,
            'result' => $settings,
            'types' => $this->types,
            'encryption_types' => $this->encryption_types
        ));
    }

    /**
     * Test email action
     *
     * @param EmailManager $emailManager
     * @return object
     */
    public function test(EmailManager $emailManager)
    {
        /*BEGIN CHECK PERMISSION*/
        if ($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('id');

        try {
            $settings = EmailAccount::findOrFail($id);
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the logged in user*/
        $user = Auth::User();

        $marketing = false;

        if($settings->type == 'marketing') {
            $marketing = true;
        }

        /*Set email settings*/
        $emailManager->setEmailSettings($marketing);

        $response = null;

        $test_email = Config::get('mail')['test_email'];

        $array = array(
            'subject' => $test_email['subject'],
            'content' => $test_email['content'],
            'to_email_address' => $user->email,
            'to_name' => $user->name,
            'sent_from_module' => 'Email accounts'
        );

        try {
            $response = $emailManager->sendHtmlEmail($array);
        } catch (\Exception $ex) {
            FlashMessengerHelper::addErrorMessage('Unable to send the test email !');

            return Redirect::action('Configuration\Email\EmailAccountController@view', $paginationData);
        }

        if($response != null) {
            return Redirect::action('Configuration\Email\EmailAccountController@view', $paginationData)->withInput()->withErrors($response);
        }

        FlashMessengerHelper::addSuccessMessage('A test email has been successfully sent to '. $user->email .'!');

        return Redirect::action('Configuration\Email\EmailAccountController@view', $paginationData);
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
            $settings = EmailAccount::findOrFail($id);
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Decrypt password*/
        if(!empty($settings->password)) {
            $settings->password = Crypt::decrypt($settings->password);
        }
        
        /*BEGIN POST*/
        if(Request::isMethod('post')) {

            $data = Request::input();

            $rules = EmailAccount::$rules;
            $messages = EmailAccount::$messages;

            /*Clear data*/
            foreach ($data as $key => $value) {
                if( (empty($value)) AND ($value !== '0') ) {
                    $data[$key] = null;
                }
            }

            /*Check type*/
            if( (isset($data['id'])) AND (!empty($data['id'])) ) {
                if( strtolower(EmailAccount::find($data['id'])->type) == strtolower($data['type']) ) {
                    $rules['type'] = 'required';
                }
            }

            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                return Redirect::action('Configuration\Email\EmailAccountController@edit', $paginationData)->withInput()->withErrors($validator);
            }

            /*Encrypt password*/
            if(!empty($data['password'])) {
                $data['password'] = Crypt::encrypt($data['password']);
            }

            $settings->update($data);

            FlashMessengerHelper::addSuccessMessage('Email account successfully updated !');

            return Redirect::action('Configuration\Email\EmailAccountController@edit', $paginationData);
        }
        /*END POST*/

        return view("configuration.email.email-account.edit", array(
            'paginationData' => $paginationData,
            'result' => $settings,
            'types' => $this->types,
            'encryption_types' => $this->encryption_types
        ));
    }

}