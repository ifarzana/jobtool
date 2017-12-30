<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Managers\Domain\DomainManager;
use App\Managers\Editor\EditorManager;
use App\Managers\Email\EmailManager;
use App\Models\Domain\Domain;
use App\Models\Domain\DomainEmailTemplate;
use App\Models\Domain\DomainSettings;
use App\Models\User\User;
use Config;
use Response;
use Request;

class ApiController extends Controller
{
    /**
     * Global settings
     *
     * @var object
     */
    protected $global_settings = null;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Domain renewal reminder
     *
     * @param $domainManager DomainManager
     *
     * @return object
     */
    public function domainReminder(DomainManager $domainManager)
    {
        $data = Request::input();

        $params = json_decode($data['params'], true);

        $key = $params['key'];

        if($key != 'reminder') {
            return Response::json(array(
                'code'      =>  403,
                'message'   =>  'Invalid key'
            ), 403);
        }

        /*Global settings*/
        $domain_settings = DomainSettings::getDomainSettings();

        $from_date = date('Y-m-d');
        $from_date = new \DateTime($from_date);
        $from_date->modify('+'.$domain_settings->days_before_renewal_due_reminder.' day');
        $from_date = $from_date->format('Y-m-d');

        $domain_array = Domain::getDueDomainForReminder($from_date);

        if(count($domain_array) > 0) {
            foreach ($domain_array as $domain_id => $flag) {

                $User = Domain::getUser($domain_id);

                /*Create job*/
                $domainManager->sendDomainEmail($User->id, $domain_id, $key);

            }

            return Response::json(array(
                'code'      =>  200,
                'message'   =>  count($domain_array)." renewal reminder emails sent"
            ), 200);

        }else{
            return Response::json(array(
                'code'      =>  200,
                'message'   =>  'No reminders to send'
            ), 200);
        }

    }

    /**
     * Send domain assignment + renewal email
     *
     * @param $emailManager EmailManager
     * @param $editorManager EditorManager
     *
     * @return object
     */
    public function sendDomainEmail(EmailManager $emailManager, EditorManager $editorManager)
    {
        $data = Request::input();

        $params = json_decode($data['params'], true);

        $user_id = $params['user_id'];
        $domain_id = $params['domain_id'];
        $key = $params['key'];

        /*Get the user*/
        $User = User::find($user_id);

        /*Get the domain*/
        $Domain = Domain::find($domain_id);

        if($User == null) {
            return Response::json(array(
                'code'      =>  403,
                'message'   =>  'Invalid user'
            ), 403);
        }

        /*Get the email template*/
        $DomainEmailTemplate = DomainEmailTemplate::where('key', $key)->first();

        $array = array(
            'subject' =>  $DomainEmailTemplate->subject,
            'content' => $editorManager->render($DomainEmailTemplate->body, $User),
            'to_name' => $User->name,
            'to_email_address' => $User->email,
            'sent_from_module' => 'Domains'
        );

        /*Set email settings*/
        $emailManager->setEmailSettings(false);

        $response = $emailManager->sendHtmlEmail($array);
        if($response == null) {
            $message = 'Email sent';
            $code = 200;


        }else{
            $message = $response;
            $code = 403;

        }

        return Response::json(array(
            'code'      =>  $code,
            'message'   =>  $message
        ), $code);


    }

}