<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Calendar</h2>
        </div>
    </div>

    <link rel="stylesheet" href="{{ URL::asset('css/plugins/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/plugins/fullcalendar/scheduler/scheduler.min.css') }}">
    <script type="text/javascript" src="{!! asset('js/plugins/fullcalendar/fullcalendar.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/plugins/fullcalendar/scheduler/scheduler.min.js') !!}"></script>
    

    <div class="wrapper wrapper-content">
        <div class="container content">

            <div class="row">
                <div class="col-lg-12">

                    <div class="cipanel">

                        <div class="cipanel-title">

                            <div class="cipanel-tools">
                                <a class="btn btn-primary btn-xs panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip"
                                   data-original-title="Refresh" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData); ?>">
                                    <span class="fa fa-refresh"></span>
                                </a>
                            </div>

                        </div>

                        <div class="cipanel-content">

                            <div class="table-responsive">
                                <div id='calendar'></div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
         </div>
    </div>

    <script type="text/javascript" src="{!! asset('js/task/task-calendar.min.js') !!}"></script>

    @include('task.templates.calendar.calendar-modal')
    @include('task.templates.calendar.create-task-modal')

@endsection