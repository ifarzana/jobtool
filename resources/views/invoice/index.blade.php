<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Invoices</h2>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cipanel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">
                                <a href="<?php echo UrlHelper::getUrl($controller, $action); ?>"
                                   class="btn btn-primary btn-xs">Reset all filters</a>
                            </div>
                        </div>
                        <div class="cipanel-content">

                            @include('templates.search')

                            <?php if(count($results)): ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'id'), true); ?>">Reference <?php if ($order_by == 'id'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a></th>
                                        <th>
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'created_at'), true); ?>">Date <?php if ($order_by == 'created_at'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'client_id'), true); ?>">Client <?php if ($order_by == 'client_id'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'total'), true); ?>">Total <?php if ($order_by == 'total'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'sent'), true); ?>">Sent <?php if ($order_by == 'sent'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th><a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'status_id'), true); ?>">Status <?php if ($order_by == 'status_id'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    @foreach ($results as $result)

                                        <?php
                                        /*Received*/
                                        $result->received = $result->getTotalReceived();

                                        /*Refunded*/
                                        $result->refunded = $result->getTotalRefunded();

                                        /*Outstanding*/
                                        $result->outstanding = $result->outstanding();

                                        /*Customer*/
                                        $customer = $result->customer;

                                        /*Status*/
                                        $status = $result->status;

                                        /*Type*/
                                        $type = $result->type;
                                        ?>

                                        <tr>
                                            <td class="td-one">
                                                <a class="btn btn-xs btn-outline btn-primary tip-bottom" data-toggle="tooltip" data-placement="bottom" title="View"
                                                   href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData, array('id' => $result->id)); ?>">
                                                    <span class="fa fa-eye"></span>
                                                </a>
                                            </td>

                                            <td><strong>{{ $result->reference }}</strong>

                                                <?php
                                                /*dump($type->is_domain_type, $result->type->name);*/
                                                ?>

                                                <small style="display: block;">
                                                    @if($type->is_domain_type == 1)
                                                        {{ $result->type->name." (# ".($result->domain ? $result->domain->id : "").")" }}
                                                    @endif

                                                    @if($result->fully_refunded == 1)
                                                        <small class="bold-no-colour text-danger" style="display: block;">
                                                            FULLY REFUNDED
                                                        </small>
                                                    @endif
                                                </small>
                                            </td>

                                            <td>{{ date_format(date_create($result->created_at), 'd-M-Y')  }}</td>

                                            <td>{{ $result->client->name }}</td>
                                            <td>{{ $result->currency->symbol.sprintf('%0.2f', $result->total) }}</td>
                                            <td>
                                                @if($result->sent == 1)
                                                    <span><i class="fa fa-check text-success"></i> {{ date_format(date_create($result->sent_at), 'd-M-Y H:i') }}</span>
                                                    <small style="display: block" class="bold">
                                                        @if($result->email_sent == 1)
                                                            EMAIL
                                                        @else
                                                            POST
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-danger"><i class="fa fa-ban"></i> <span class="bold-no-colour">NOT SENT</span></span>
                                                @endif
                                            </td>

                                            <td>
                                        <span class="bold-no-colour {{ $status->status_class }}">
                                            {{ strtoupper($status->name) }}
                                        </span>
                                            </td>

                                            <td class="td-three text-right">

                                                <?php if(AclManagerHelper::hasPermission('read')): ?>
                                                    <a class="btn btn-xs btn-outline btn-warning"
                                                       href="<?php echo UrlHelper::getUrl($controller, 'download', $paginationData, array('id' => $result->id)); ?>">
                                                        Download
                                                    </a>
                                                <?php endif; ?>

                                                <?php if(AclManagerHelper::hasPermission('delete')): ?>
                                                    <a class="btn btn-xs btn-outline btn-danger"
                                                       href="javascript:confirmation('<?php echo UrlHelper::getUrl($controller, 'cancel', $paginationData, array('id' => $result->id)); ?>', 'Cancel domain ?')">
                                                        Cancel
                                                    </a>
                                                <?php endif; ?>

                                            </td>
                                        </tr>

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- BEGIN PAGINATION -->
                            <?php if($paginated): ?>
                            @include('templates.pagination', ['paginator' => $results])
                            <?php endif; ?>
                            <!-- END PAGINATION -->

                            <?php else: ?>
                            <h4 class="text-center text-danger" style="padding-top: 15px;">No results found</h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection