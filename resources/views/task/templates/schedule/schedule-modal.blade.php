<div class="modal inmodal" id="schedule-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h2 class="modal-title text-left">New task</h2>
            </div>

            {!! Form::open(['method' => 'GET', 'url' => UrlHelper::getUrl($controller, 'bookTime', $paginationData, array())]) !!}

            {!! Form::hidden('user_id', null) !!}
            {!! Form::hidden('from_date', null) !!}
            {!! Form::hidden('to_date', null) !!}

            <div class="modal-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('user_name', 'User name') !!}
                            {!! Form::text('user_name', null, ['class' => 'form-control', 'required', 'readonly']) !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('client_id', 'Client*') !!}
                            {!! Form::select('client_id', [null => 'Please choose'] + $clients, null, ['class' => 'form-control', 'id' => 'client_id', 'required']) !!}

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('project_id', 'Project') !!}
                            {!! Form::select('project_id', [null => 'Please choose'], null, ['class' => 'form-control', 'id' => 'project_id']) !!}

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('start_date', 'Start date') !!}

                            <div class="input-group">
                                {!! Form::text('start_date',null,['class'=>'form-control date', 'readonly']) !!}
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('start_time', 'Start time') !!}

                            <div class="input-group">
                                {!! Form::text('start_time',null,['class'=>'form-control', 'readonly']) !!}
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('end_date', 'End date') !!}

                            <div class="input-group">
                                {!! Form::text('end_date',null,['class'=>'form-control date', 'readonly']) !!}
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('end_time', 'End time') !!}

                            <div class="input-group">
                                {!! Form::text('end_time',null,['class'=>'form-control', 'readonly']) !!}
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('title', 'Task title*') !!}
                            {!! Form::text('title', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('hours_per_day', 'Hours per day') !!}
                            {!! Form::text('hours_per_day', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('job_id', 'Job') !!}
                            {!! Form::select('job_id', [null => 'Please choose'], null, ['class' => 'form-control', 'id' => 'job_id']) !!}

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('description', 'Description') !!}
                            {!! Form::text('description', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    </div>
</div>