
<!-- BEGIN DATE CREATE TEMPLATE -->
@if(request()->get('new_renewal'))
    @include('domain.new-renewal')
@endif
<!-- END DATE CREATE TEMPLATE -->


<?php if(count($renewal_entries)): ?>

<div class="table-responsive">
    <table class="table table-striped domain-table-margin-top">
        <thead>
        <tr>
            <th>Invoice</th>
            <th>Title</th>
            <th>Cost</th>
            <th>Interval</th>
            <th>Renewal date</th>
            <th>Next due date</th>
            <th>Added by</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($renewal_entries as $renewal_entry)
            <tr>
                <td>
                     <a href="<?php echo UrlHelper::getUrl('Invoice\InvoiceController', 'view', $paginationData, array('id' => $renewal_entry->invoice_id)); ?>">
                         {{ $renewal_entry->invoice? $renewal_entry->invoice->reference: '' }}
                     </a>
                    @if($renewal_entry->invoice)
                        <p>
                            @if($renewal_entry->invoice->sent == 1)
                                <span><i class="fa fa-check text-success"></i> {{ date_format(date_create($renewal_entry->invoice->sent_at), 'd-M-Y H:i') }}</span>
                                <small style="display: block" class="bold">
                                    @if($renewal_entry->invoice->email_sent == 1)
                                        EMAIL
                                    @else
                                        POST
                                    @endif
                                </small>
                            @else
                                <span class="text-danger"><i class="fa fa-ban"></i> <span class="bold-no-colour">Not sent</span></span>
                            @endif
                        </p>
                    @endif
                </td>
                <td>
                    Domain renewal for {{ $renewal_entry->renewal_period }} {{ ($renewal_entry->interval) ? $renewal_entry->interval->name.(($renewal_entry->renewal_period >1)? 's' : '') : 'N/A' }}
                </td>
                <td>
                    {{ $renewal_entry->cost }}
                </td>
                <td>
                    {{ $renewal_entry->renewal_period }} {{ ($renewal_entry->interval) ? $renewal_entry->interval->name : 'N/A' }}
                </td>
                <td>
                    {{ date_format(date_create($renewal_entry->renewal_date), 'd-M-Y') }}
                </td>
                <td>
                    {{ date_format(date_create($renewal_entry->expiry_date), 'd-M-Y') }}
                </td>

                <td>{{ $renewal_entry->added_by_user_name }}
                    <small class="bold" style="display: block;">
                        {{ date_format(date_create($renewal_entry->created_at), 'd-M-Y H:i') }}
                    </small>
                </td>

            </tr>

        @endforeach
        </tbody>

    </table>
</div>

<?php else: ?>
<h4 class="text-center" style="padding: 10px 0px; font-weight: 300"></h4>
<?php endif; ?>


<?php
/*<div class="text-center">
    <a class="btn btn-primary btn-xs" type="button"
       href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData, array('id' => $result->id, 'new_renewal' => 1)); ?>">
        <i class="fa fa-plus"></i>&nbsp;&nbsp;<span class="bold">Add domain renewal entry</span></a>
</div>*/
?>