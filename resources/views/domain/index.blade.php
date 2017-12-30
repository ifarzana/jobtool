<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Domains</h2>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cipanel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">

                                <a href="<?php echo UrlHelper::getUrl($controller, 'create', $paginationData); ?>"
                                   class="btn btn-primary btn-xs">New domain registration</a>

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
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'name'), true); ?>">Name <?php if ($order_by == 'name'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a></th>
                                        <th>
                                            <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'client_id'), true); ?>">Client <?php if ($order_by == 'client_id'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th><a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'status_id'), true); ?>">Status <?php if ($order_by == 'status_id'): ?>
                                                <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                            </a>
                                        </th>
                                        <th>Due date</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($results as $result)

                                        <tr class = "@if($result->status_id == 2) gray-tr @endif">
                                            <td class="td-one">
                                                <a class="btn btn-xs btn-outline btn-primary tip-bottom" data-toggle="tooltip" data-placement="bottom" title="View"
                                                   href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData, array('id' => $result->id)); ?>">
                                                    <span class="fa fa-eye"></span>
                                                </a>
                                            </td>

                                            <td><strong>{{ $result->name }}</strong>
                                                <?php $User = $result->getUser($result->id); ?>
                                                <p>
                                                    <small> Assigned to: </small><a href="<?php echo UrlHelper::getUrl('Configuration\User\UserController', 'view', array(), array('id' => $User->id)); ?>">{{ $User->name }}</a>

                                                </p>
                                            </td>

                                            <td>
                                                {{ $result->client->name }}
                                            </td>

                                            <td>
                                                @if($result->status_id == 2) <span class="text-danger"><i class="fa fa-clock-o"></i></span> @endif {{ $result->status->name }}
                                            </td>

                                            <td>
                                                <h4 class="text-date">{{ date_format(date_create($result->expiry_date), 'd-M-Y')  }}</h4>
                                            </td>

                                            <td class="td-two text-right">

                                                <?php if(AclManagerHelper::hasPermission('update')): ?>
                                                <a class="btn btn-xs btn-outline btn-warning"
                                                   href="<?php echo UrlHelper::getUrl($controller, 'edit', $paginationData, array('id' => $result->id)); ?>">
                                                    Edit
                                                </a>
                                                <?php endif; ?>

                                                <?php if(AclManagerHelper::hasPermission('delete')): ?>
                                                <a class="btn btn-xs btn-outline btn-danger"
                                                   href="javascript:confirmation('<?php echo UrlHelper::getUrl($controller, 'delete', $paginationData, array('id' => $result->id)); ?>', 'Delete domain ?')">
                                                    Delete
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