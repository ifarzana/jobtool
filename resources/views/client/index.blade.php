<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Clients</h2>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cipanel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">

                                <?php if(AclManagerHelper::hasPermission('create')): ?>
                                    <a href="<?php echo UrlHelper::getUrl($controller, 'create', $paginationData); ?>"
                                       class="btn btn-primary btn-xs">New client</a>
                                <?php endif; ?>

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
                                                    <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'isActive'), true); ?>">Active <?php if ($order_by == 'isActive'): ?>
                                                        <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                                    </a></th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($results as $result)

                                                <tr>
                                                    <td class="td-one">
                                                       <a class="btn btn-xs btn-outline btn-primary tip-bottom" data-toggle="tooltip" data-placement="bottom" title="View"
                                                           href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData, array('id' => $result->id)); ?>">
                                                            <span class="fa fa-eye"></span>
                                                        </a>

                                                    </td>

                                                    <td>{{ $result->name }}</td>

                                                    <td>
                                                        @if(!empty($result->isActive))
                                                            {{ $result->isActive }}
                                                        @else

                                                        @endif
                                                    </td>

                                                    <td class="td-four text-right">

                                                        <a class="btn btn-xs btn-outline btn-info"
                                                           href="<?php echo UrlHelper::getUrl('Client\ClientController', 'hostings', $paginationData, array('id' => $result->id)); ?>">
                                                            Hostings
                                                        </a>

                                                        <a class="btn btn-xs btn-outline btn-primary"
                                                           href="<?php echo UrlHelper::getUrl('Client\ClientController', 'domains', $paginationData, array('client_id' => $result->id)); ?>">
                                                            Domains
                                                        </a>

                                                        <?php if(AclManagerHelper::hasPermission('update')): ?>
                                                            <a class="btn btn-xs btn-outline btn-warning"
                                                               href="<?php echo UrlHelper::getUrl($controller, 'edit', $paginationData, array('id' => $result->id)); ?>">
                                                                Edit
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if(AclManagerHelper::hasPermission('delete')): ?>
                                                            <a class="btn btn-xs btn-outline btn-danger"
                                                               href="javascript:confirmation('<?php echo UrlHelper::getUrl($controller, 'delete', $paginationData, array('id' => $result->id)); ?>', 'Delete client ?')">
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