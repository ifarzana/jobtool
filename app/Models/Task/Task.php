<?php

namespace App\Models\Task;

use App\Models\Client\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Task extends Model
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'TSK_task';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'created_at';

    /**
     * The default order column
     *
     * @var string
     */
    protected $default_order = 'DESC';

    /**
     * The group by column
     *
     * @var string
     */
    protected $group_by = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'status_id', 'user_id', 'client_id', 'job_id', 'project_id', 'start_date', 'start_time', 'end_date', 'end_time'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'title' => 'required',
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
    //
    );

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    /**
     * Fetch all function
     *
     * @param $query Builder
     * @param $paginationData array
     * @param $search_by string
     * @param $where array
     * @return object
     */
    public function scopefetchAll(Builder $query, $paginationData = array(), $search_by, $where = array())
    {
        /*BEGIN SEARCH*/
        if($search_by != null) {
            $results = $this::search(urldecode($search_by));
        }else{
            $results = $this;
        }
        /*END SEARCH*/

        /*BEGIN WHERE*/
        if(!empty($where)) {
            $results = $results->where($where);
        }
        /*END WHERE*/

        /*BEGIN PAGINATION*/
        $paginated = isset($paginationData['paginated']) ? $paginationData['paginated']: false;

        if($paginated) {

            $paginationConfig = Config::get('pagination.pagination');

            $order_by         = $this->getOrderBy($paginationData);
            $order            = $paginationData['order'];
            $p                = $paginationData['p'];
            $itemCountPerPage = $paginationConfig['itemCountPerPage'];

            $results = $results->orderBy($order_by, $order)->paginate($itemCountPerPage, ['*'], 'p');

            return $results;

        }else {
            if( (isset($paginationData['order_by'])) && (isset($paginationData['order'])) ) {
                $results = $results->orderBy($this->getOrderBy($paginationData), $paginationData['order']);
            }else{
                $results = $results->orderBy($this->default_order_by, $this->default_order);
            }
        }

        /*END PAGINATION*/

        return $results->get();
    }

    /**
     * Scope within dates
     *
     * @param $query Builder
     * @param $start_date string
     * @param $end_date string
     *
     * @return object
     */
    public function scopeWithinDates(Builder $query, $start_date, $end_date) {

        $query
            ->join('UM_user','TSK_Task.user_id', '=', 'UM_user.id' , 'left')
            ->join('UM_user_group','UM_user.group_id', '=', 'UM_user_group.id' , 'left')
            ->join('TSK_task_status','TSK_task.status_id', '=', 'TSK_task_status.id' , 'left')
            ->where('TSK_Task.start_date', '<=', $end_date)
            ->where('TSK_Task.end_date', '>=', $start_date);

        return $query;
    }

    /**
     * Returns all the tasks for the schedule
     *
     * @param $users array
     * @param $from_date string
     * @param $to_date string
     *
     * @return object
     */
    public static function getScheduleTasks($users, $from_date, $to_date)
    {
        $columns = array(
            'TSK_task.*',
            'UM_user.name as user_name',
            'TSK_task_status.code as status',
        );

        $results = Task::orderBy('start_date')
            ->select($columns)
            ->withinDates($from_date, $to_date)
            ->whereIn('TSK_task.user_id', array_pluck($users, 'id'))
            ->get()->toArray();

        return $results;
    }

}