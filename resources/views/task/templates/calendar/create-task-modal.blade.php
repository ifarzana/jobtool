<div class="modal inmodal" id="create-task-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="modal-title">Create new ask </h4>
            </div>

            {!! Form::open() !!}

            <div class="modal-body clearfix">

                <div class="form-group col-md-12">

                    <div class="input-group form-input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-users"></i>
                            </span>

                        {!! Form::text('task-title', null, ['class' => 'form-control']) !!}
                    </div>

                </div>

                <div class="form-group col-md-12">

                    <div class="input-group form-input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-book"></i>
                                </span>

                        {!! Form::text('task-resource', null, ['class' => 'form-control']) !!}
                    </div>

                </div>

            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="task-submit" class="btn btn-primary">Submit</button>
            </div>

            {!! Form::close() !!}



        </div>
    </div>
</div>