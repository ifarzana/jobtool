<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

$readonly = 'readonly';

$paginationData2 = $paginationData;
if(isset($paginationData2['new_renewal']))
{
    unset($paginationData2['new_renewal']);
}
?>

@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>View domain</h2>
            <strong>Client name:</strong> {{ $result->client->name }}
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="cipanel">

                <div class="cipanel-title">
                    <div class="cipanel-tools">
                        <a class="btn btn-primary btn-xs panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip"
                           data-original-title="Back" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData2); ?>">
                            <span class="fa fa-arrow-left"></span>
                        </a>

                        <?php if(AclManagerHelper::hasPermission('update')): ?>
                        <a href="<?php echo UrlHelper::getUrl($controller, 'edit', $paginationData, array('id' => $result->id)); ?>"
                           class="btn btn-primary btn-xs">Edit</a>
                        <?php endif; ?>

                        <?php if(AclManagerHelper::hasPermission('delete')): ?>
                        <a href="javascript:confirmation('<?php echo UrlHelper::getUrl($controller, 'delete', $paginationData, array('id' => $result->id)); ?>', 'Delete domain ?')"
                           class="btn btn-primary btn-xs">Delete</a>
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

                            <h2 class="font-bold m-b-xs">
                                {{$result->client->name}}
                            </h2>
                            <strong>Domain:</strong> <span class="domain-name">{{ $result->name }}</span>
                            <div class="m-t-md">
                                <!-- <h2 class="product-main-price">$406,602 <small class="text-muted">Expiry date</small> </h2> -->
                            </div>

                            <div class="m-b-md">
                                <h2 class="form-section-title">Registration details</h2>
                                <hr class="text-subline">
                            </div>

                            <dl class="dl-horizontal">
                                <dt class="view-dt">Status:</dt> <dd class="view-dd"><span class="span @if($result->status_id ==1) span-success @else span-danger @endif span-rounded span-outline ">{{ $result->status->name }}</span></dd>
                            </dl>

                        </div>
                        <div class="col-lg-7">
                            <dl class="dl-horizontal">

                                <dt class="view-dt">Registration date:</dt> <dd class="view-dd">{{ $result->registration_date }}</dd>
                                <dt class="view-dt">Registration period:</dt> <dd class="view-dd">{{ $result->registration_period.' '.$result->interval->name.(($result->registration_period >1)? 's' : '') }}</dd>
                                <dt class="view-dt">Created by:</dt> <dd class="view-dd">{{ Auth::user()->name }}</dd>
                                <?php $AssignedUser = $result->getUser($result->id); ?>
                                <dt class="view-dt">Assigned to:</dt> <dd class="view-dd"><a href="<?php echo UrlHelper::getUrl('Configuration\User\UserController', 'view', array(), array('id' => $AssignedUser->id)); ?>">{{ $AssignedUser->name }}</a></dd>
                                <dt class="view-dt">Client:</dt> <dd class="view-dd"><a href="#" class="text-navy"> {{$result->client->name}}</a> </dd>
                                <dt class="view-dt">Contact:</dt> <dd class="view-dd"> {{$result->contact ? $result->contact->name : ''}}</dd>
                                <dt class="view-dt">Registration cost:</dt> <dd class="view-dd">{{$result->currency->symbol.$result->cost}}</dd>
                                <dt class="view-dt">Invoice:</dt> <dd class="view-dd"><small>{{$Invoice ? $Invoice->filename : ''}}</small>

                                    <span class="span @if($Invoice) @if($Invoice->status->id == 1) span-success @elseif($Invoice->status->id == 2) span-warning @elseif($Invoice->status->id == 3) span-info @elseif($Invoice->status->id == 4) span-danger @endif @endif span-outline span-xs ">{{$Invoice ? $Invoice->status->name : ''}}</span></dd>
                            </dl>
                        </div>
                        <div class="col-lg-5" id="cluster_info">
                            <dl class="dl-horizontal">

                                <dt class="view-dt">SSL Certificate:</dt> <dd class="view-dd">{{$result->ssl}}</dd>
                                <dt class="view-dt">Registrar:</dt> <dd class="view-dd">{{$result->registrar ? $result->registrar->name: ''}}</dd>
                                <dt class="view-dt">Server:</dt> <dd class="view-dd">{{$result->server ? $result->server->name : ''}}</dd>
                                <dt class="view-dt">CMS:</dt> <dd class="view-dd">{{$result->cms}}</dd>

                            </dl>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="form-section-title">Upcoming renewal</h2>
                            <hr class="text-subline">

                            <dl class="dl-horizontal">
                                <dt class="view-dt">Expiry date:</dt> <dd class="view-dd">{{date_format(date_create($due_date), 'd-M-Y')}}
                                    <a class="btn btn-primary" type="button" href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData, array('id' => $result->id, 'new_renewal' => 1)); ?>">
                                        <span class="bold">Renew</span></a>
                                </dd>

                            </dl>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="form-section-title">Renewal entries</h2>
                            <hr class="text-subline">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs">

                                    <li class="active"><a data-toggle="tab" href="#tab-domain-renewal">Renewal</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-domain-transfer">Transfer</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-domain-renewal" class="tab-pane active">

                                        <!-- BEGIN RENEWAL TEMPLATE -->
                                    @include('domain.renewal')
                                    <!-- END RENEWAL TEMPLATE -->

                                    </div>
                                    <div id="tab-domain-transfer" class="tab-pane">
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