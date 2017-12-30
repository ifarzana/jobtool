

<div class="new-renewal">
    <div class="row">

        <div class="col-md-12">

                <?php
                    $paginationData2 = $paginationData;
                    if(isset($paginationData2['new_renewal']))
                    {
                        unset($paginationData2['new_renewal']);
                    }
                ?>

                <h2 class="area-title">Create renewal entry</h2>

                {!! Form::open(['method' => 'POST', 'url' => UrlHelper::getUrl($controller, 'createRenewalEntry', $paginationData)]) !!}

                {!! Form::hidden('id', null) !!}
                {!! Form::hidden('domain_id', $result->id) !!}

                    <div class="col-md-6">
                        <div class="form-group <?php if($errors->has('renewal_date')){ echo "has-error";} ?>">
                            {!! Form::label('renewal_date', 'Renewal date') !!}

                            <div class="input-group">
                                {!! Form::text('renewal_date', date_format(date_create($due_date), 'd-M-Y'), ['class'=>'form-control date', 'required']) !!}
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group <?php if($errors->has('renewal_period')){ echo "has-error";} ?>">
                            {!! Form::label('renewal_period', 'Renewal period') !!}
                            {!! Form::number('renewal_period', 1, ['class'=>'form-control', 'required', 'min' => 1, 'max' => 99, 'step' => 1,]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group <?php if($errors->has('interval_id')){ echo "has-error";} ?>">
                            {!! Form::label('interval_id', 'Interval') !!}
                            {!! Form::select('interval_id', [null => 'Please choose'] + $intervals, null,['class'=>'form-control', 'required']) !!}
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group <?php if($errors->has('cost')){ echo "has-error";} ?>">
                            {!! Form::label('cost', 'Cost*') !!}
                            {!! Form::text('cost', null,['class'=>'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?php if($errors->has('status_id')){ echo "has-error";} ?>">
                            {!! Form::label('status_id', 'Active*') !!}
                            {!! Form::select('status_id', [0 => 'No', 1 => 'Yes'], 1, ['class'=>'form-control', 'required']) !!}
                        </div>
                    </div>

                    <div class="col-xs-12 text-right">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a class="btn btn-primary" href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData2, array('id' => $result->id)); ?>">x Close</a>
                    </div>

                {!! Form::close() !!}

        </div>
    </div>
</div>

<script>
    $('.form-control.date').datepicker({
        format: "dd-M-yyyy",
        todayBtn: false,
        autoclose: true,
        todayHighlight: true
    });
</script>