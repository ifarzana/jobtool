<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Task schedule</h2>
        </div>
    </div>

    <link rel="stylesheet" href="{{ URL::asset('css/task/schedule.min.css') }}">

    <div class="wrapper wrapper-content">
        <div class="container content">

            <div class="row">
                <div class="col-lg-12 schedule-col">

                    <div class="cipanel booking-schedule-panel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">
                                <a class="btn btn-primary btn-xs panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip"
                                   data-original-title="Refresh" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData); ?>">
                                    <span class="fa fa-refresh"></span>
                                </a>
                            </div>
                        </div>

                        <div class="cipanel-content booking-schedule-pb-mobile">

                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h4 class="text-center"><i class="fa fa-exclamation-circle"></i> Schedule is not available on low resolutions</h4>
                                </div>
                            </div>

                        </div>

                        <div class="cipanel-content  booking-schedule-pb">


                            @if (isset($errors) && count($errors) > 0)
                                <div class="alert alert-danger alert-list" role="alert">
                                    <p>There were one or more issues with your submission. Please correct them as indicated below.</p>

                                    <ul>
                                        @foreach ($errors as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>

                                </div>
                            @endif


                                <div class="row">
                                    <div class="col-md-12">
                                        @include('task.templates.schedule.schedule')
                                    </div>
                                </div>

                        </div>

                    </div>
                </div>

            </div>

         </div>
    </div>

    <script type="text/javascript" src="{!! asset('js/task/task-schedule.min.js') !!}"></script>

    <script>
        $('.form-control.date').datepicker({
            format: "dd-M-yyyy",
            todayBtn: false,
            autoclose: true,
            todayHighlight: true
        });
    </script>

@endsection