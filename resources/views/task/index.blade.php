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

    <link rel="stylesheet" href="{{ URL::asset('css/task/schedule.css') }}">

    <div class="wrapper wrapper-content">
        <div class="container-fluid content">

            <div class="row-fluid">
                <div class="col-lg-12 schedule-col large-col">

                    <div class="cipanel task-schedule-panel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">
                                <a class="btn btn-primary btn-xs panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip"
                                   data-original-title="Refresh" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData); ?>">
                                    <span class="fa fa-refresh"></span>
                                </a>
                            </div>
                        </div>

                        <div class="cipanel-content task-schedule-pb-mobile">

                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h4 class="text-center"><i class="fa fa-exclamation-circle"></i> Schedule is not available on low resolutions</h4>
                                </div>
                            </div>

                        </div>

                        <div class="cipanel-content task-schedule-pb">

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

                            {!! Form::open(['method' => 'POST', 'url' => UrlHelper::getUrl($controller, $action, $paginationData)]) !!}

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('from_date', 'From date*') !!}

                                        <div class="input-group">
                                            {!! Form::text('from_date', $submitted_data['from_date'], ['class'=>'form-control date', $readonly, "onchange='this.form.submit()'"]) !!}
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('to_date', 'To date*') !!}

                                        <div class="input-group">
                                            {!! Form::text('to_date', $submitted_data['to_date'], ['class'=>'form-control date', $readonly, "onchange='this.form.submit()'"]) !!}
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {!! Form::close() !!}

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

    <script type="text/javascript" src="{!! asset('js/task/task-schedule.js') !!}"></script>

    <script>
        $('.form-control.date').datepicker({
            format: "dd-M-yyyy",
            todayBtn: false,
            autoclose: true,
            todayHighlight: true
        });
    </script>

@endsection