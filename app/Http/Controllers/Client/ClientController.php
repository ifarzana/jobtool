<?php 

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Client\ClientContact;
use App\Models\Domain\Domain;
use App\Models\Hosting\Hosting;
use Redirect;
use Request;
use Validator;
use Config;
use Response;
use App\Models\Client\Client;
use App\Helpers\UrlHelper;

class ClientController extends Controller
{
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

        $results = Client::fetchAll($paginationData, request()->get('search_by'), array());

        return view("client.index", array(
            'paginationData' => $paginationData,
            'results' => $results
        ));
    }

    /**
     * Client's domains
     *
     * @return object
     */
    public function domains()
    {
        /*BEGIN CHECK PERMISSION*/
        if($this->checkPermission('read') == false) {
            return redirect('/auth/logout');
        }
        /*END CHECK PERMISSION*/

        /*BEGIN PAGINATION & SEARCH*/
        $paginationData = UrlHelper::setArray($this->pagination_array);
        /*END PAGINATION & SEARCH*/

        $id = request()->get('client_id');

        try {
            $client = Client::findOrFail($id);
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the domains*/
        $results = Domain::where('client_id', $client->id)->orderBy('created_at', 'DESC')->get();

        return view("client.domains", array(
            'paginationData' => $paginationData,
            'results' => $results,
            'client' => $client
        ));
    }

    /**
     * Client's hostings
     *
     * @return object
     */
    public function hostings()
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
            $client = Client::findOrFail($id);
        } catch (\Exception $ex) {
            return redirect('/auth/logout');
        }

        /*Get the hostings*/
        $results = Hosting::where('client_id', $client->id)->get();

        return view("client.hostings", array(
            'paginationData' => $paginationData,
            'results' => $results,
            'client' => $client
        ));
    }

    /**
     * Search ajax action
     *
     * @return object
     */
    public function searchAjax()
    {
        $query = request()->get('query');

        $Clients = Client::fetchAll(array('paginated' => false, 'order_by' => 'id', 'order' => 'ASC'), $query, array(), 50);

        $array = array();

        foreach ($Clients as $client) {

            $address = null;

            if(!empty($client->address_line_1)) {

                $address = $client->address_line_1 . ", ";

                if(!empty($client->address_line_2)) {
                    $address.= $client->address_line_2 . ", ";
                }

            }else{

                if(!empty($client->address_line_2)) {
                    $address = $client->address_line_2 . ", ";
                }
            }

            if(!empty($client->town)) {
                $address .= $client->town . ", ";
            }

            $array[] = array(
                'id' => $client->id,
                'name' => $client->name."  (" . $client->id .")",
                'top' => $client->name."  (" . $client->id .")"."_#_".
                    $address.$client->country->name.", ".$client->postcode."_#_"
            );
        }

        return Response::json($array);
    }

    /**
     * Return the client record
     *
     * @return object
     */
    public function getAjax() {

        $data = Request::input();

        $id = preg_replace('/\s/', '', $data['id']);

        $client = Client::find((int)$id);

        $array = $client->toArray();

        /*Get all the contacts of a client*/
        $contacts = ClientContact::where('client_id', $client->id)->get();

        /*Default*/
        $array['contacts'][] = array(
            'id' => 'N/A',
            'text' => 'N/A'
        );

        if(count($contacts) > 0) {
            foreach($contacts as $contact){
                $select = array(
                    'id' =>   $contact->id,
                    'text'=> $contact->name
                );
                $array['contacts'][] = $select;
            }
        }



        //$array['contacts'] = $select;

        //{"1":"Catalin","2":"Israt Jahan Farzana","4":"John Doe"}}
        //{ id: 0, text: 'item1' }, { id: 1, text: 'item2' }
        //{"id":1, "name":"Astra Automotive"}

        return Response::json($array);
    }

}