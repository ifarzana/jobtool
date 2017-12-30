<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

$readonly = 'readonly';
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>View email account</h2>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">

                    <div class="cipanel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">
                                <a class="btn btn-primary btn-xs panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip"
                                   data-original-title="Back" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData); ?>">
                                    <span class="fa fa-arrow-left"></span>
                                </a>

                                <?php if(AclManagerHelper::hasPermission('update')): ?>
                                <a href="<?php echo UrlHelper::getUrl($controller, 'edit', $paginationData, array('id' => $result->id)); ?>"
                                   class="btn btn-primary btn-xs">Edit</a>
                                <?php endif; ?>

                            </div>
                        </div>

                        <div class="cipanel-content">

                            @if (isset($errors) && count($errors) > 0)
                                <div class="alert alert-danger alert-list" role="alert">
                                    <p>There were one or more issues with your submission. Please correct them as indicated below.</p>

                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>

                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">

                                    {!! Form::model($result) !!}

                                    {!! Form::hidden('id', null) !!}

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="form-section-title">Summary</h2>
                                            <hr class="text-subline">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group <?php if($errors->has('type')){ echo "has-error";} ?>">
                                                {!! Form::label('type', 'Account type*') !!}
                                                {!! Form::select('type', $types, null, ['class' => 'form-control', 'required', $readonly]) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="form-section-title">Sender</h2>
                                            <hr class="text-subline">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group <?php if($errors->has('from_name')){ echo "has-error";} ?>">
                                                {!! Form::label('from_name', 'Name*') !!}
                                                {!! Form::text('from_name', null,['class'=>'form-control', 'required', $readonly]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group <?php if($errors->has('from_email')){ echo "has-error";} ?>">
                                                {!! Form::label('from_email', 'Email address*') !!}
                                                {!! Form::email('from_email',null,['class'=>'form-control', 'required', $readonly]) !!}
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="form-section-title">Reply to</h2>
                                            <hr class="text-subline">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group <?php if($errors->has('reply_to_name')){ echo "has-error";} ?>">
                                                {!! Form::label('reply_to_name', 'Name') !!}
                                                {!! Form::text('reply_to_name', null,['class'=>'form-control', $readonly]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group <?php if($errors->has('reply_to_email')){ echo "has-error";} ?>">
                                                {!! Form::label('reply_to_email', 'Email address') !!}
                                                {!! Form::email('reply_to_email',null,['class'=>'form-control', $readonly]) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="form-section-title">SMTP options</h2>
                                            <hr class="text-subline">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group <?php if($errors->has('host')){ echo "has-error";} ?>">
                                                {!! Form::label('host', 'Host*') !!}
                                                {!! Form::text('host', null,['class'=>'form-control', 'required', $readonly]) !!}
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group <?php if($errors->has('port')){ echo "has-error";} ?>">
                                                {!! Form::label('port', 'Port*') !!}
                                                {!! Form::text('port', null,['class'=>'form-control', 'required', $readonly]) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group <?php if($errors->has('encryption')){ echo "has-error";} ?>">
                                                {!! Form::label('encryption', 'Encryption type') !!}
                                                {!! Form::select('encryption', $encryption_types, null, ['class' => 'form-control', $readonly]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group <?php if($errors->has('username')){ echo "has-error";} ?>">
                                                {!! Form::label('username', 'Username') !!}
                                                {!! Form::text('username', null,['class'=>'form-control', $readonly]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group <?php if($errors->has('password')){ echo "has-error";} ?>">
                                                {!! Form::label('password', 'Password') !!}
                                                {!! Form::text('password', null,['class'=>'form-control', $readonly]) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}

                                </div>

                                <div class="row">
                                    <div class="col-xs-12 text-center">
                                        <a class="btn btn-primary" href="<?php echo UrlHelper::getUrl($controller, 'test', $paginationData, array('id' => $result->id)); ?>">
                                            <i class="fa fa-envelope"></i> Send test email
                                        </a>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection