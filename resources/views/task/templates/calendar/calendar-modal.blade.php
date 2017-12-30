<div class="modal inmodal" id="calendar-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="modal-title">Task</h4>
            </div>

            <div class="modal-body">
                <p>Title: <span id="title"></span></p>
                <p class="description">Description: <span id="description"></span></p>
                <p><i class="fa fa-calendar text-primary"></i> Start date: <span id="start_date"></span></p>
                <p><i class="fa fa-calendar text-primary"></i> End date: <span id="end_date"></span></p>

                <p class="client"><i class="fa fa-user"></i> Client: <span id="client"></span></p>
            </div>

            <div class="modal-footer">

                <?php if(AclManagerHelper::hasPermission('update')): ?>
                    <a id="edit-link" class="btn btn-warning btn-sm" href="">
                        <span class="fa fa-pencil"></span> Edit
                    </a>
                <?php endif; ?>

                <?php if(AclManagerHelper::hasPermission('delete')): ?>
                    <a id="delete-link" class="btn btn-danger btn-sm" href="">
                        <span class="fa fa-trash"></span> Delete
                    </a>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>