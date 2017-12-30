<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>User groups</h2>
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
                                   data-original-title="Back" href="<?php echo UrlHelper::getUrl('Configuration\User\UserController', 'index', array()); ?>">
                                    <span class="fa fa-arrow-left"></span>
                                </a>
                                <?php if(AclManagerHelper::hasPermission('create')): ?>
                                    <a href="<?php echo UrlHelper::getUrl($controller, 'create', $paginationData); ?>"
                                       class="btn btn-primary btn-xs">New user group</a>
                                <?php endif; ?>

                                <a href="<?php echo UrlHelper::getUrl($controller, $action); ?>"
                                       class="btn btn-primary btn-xs">Reset all filters</a>
                            </div>
                        </div>
                        <div class="cipanel-content">

                            {{--@include('templates.search')--}}

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
                                                    <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'description'), true); ?>">Description <?php if ($order_by == 'description'): ?>
                                                        <i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?>
                                                    </a></th>

                                                <?php if(AclManagerHelper::hasPermission('update')): ?>
                                                <th class="text-center">Permissions</th>
                                                <?php endif; ?>

                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach ($results as $result)

                                                <tr>
                                                    <td class="td-one">
                                                        @if($result->locked == 1)
                                                            <i class="fa fa-lock"></i>
                                                        @else
                                                            <i class="fa fa-unlock"></i>
                                                        @endif
                                                    </td>

                                                    <td>{{ $result->name }}</td>

                                                    <td>{{ $result->description }}</td>

                                                    <?php if(AclManagerHelper::hasPermission('update')): ?>
                                                    <td class="text-center">
                                                        <a class="btn btn-default btn-xs <?php if ($result->locked == 1) {
                                                            echo "disabled";
                                                        } ?>"
                                                           href="<?php echo UrlHelper::getUrl($controller, 'permissions', $paginationData, array('id' => $result->id)); ?>">
                                                            <span class="fa fa-key"></span>
                                                        </a>
                                                    </td>
                                                    <?php endif; ?>


                                                    <td class="td-two text-right">

                                                        <?php if(AclManagerHelper::hasPermission('update')): ?>
                                                            <a class="btn btn-xs btn-outline btn-warning"
                                                               href="<?php echo UrlHelper::getUrl($controller, 'edit', $paginationData, array('id' => $result->id)); ?>">
                                                                Edit
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if( (AclManagerHelper::hasPermission('delete')) AND ($result->locked != 1) ): ?>
                                                            <a class="btn btn-xs btn-outline btn-danger"
                                                               href="javascript:confirmation('<?php echo UrlHelper::getUrl($controller, 'delete', $paginationData, array('id' => $result->id)); ?>', 'Delete user group ?')">
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