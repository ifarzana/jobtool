<?php
/*BEGIN ROUTING VARIABLES*/
$routeDetails = UrlHelper::getRouteDetails();
$controller = $routeDetails['controller'];
$action = $routeDetails['action'];
/*END ROUTING VARIABLES*/

/*BEGIN PAGINATION & SEARCH*/
$order_by = $paginationData['order_by'];
$order = $paginationData['order'];
$p = $paginationData['p'];
$paginated = $paginationData['paginated'];

$url_order = $paginationData['order'] == 'ASC' ? 'DESC' : 'ASC';
/*END PAGINATION & SEARCH*/
?>

@extends('templates.default')

@section('content')

	<div class="container">

		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-default">

					<div class="panel-heading clearfix">

                        <a class="panel-button panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip" data-original-title="Back" href="<?php echo UrlHelper::getUrl('Configuration\System\SystemController', 'index', array(), array()); ?>">
                            <span class="fa fa-arrow-left"></span>
                        </a>

						<a class="panel-button panel-close tip-bottom" data-placement="bottom" data-toggle="tooltip" data-original-title="Close" href={{url('/dashboard')}}>
							<span class="fa fa-times"></span>
						</a>

                        <a class="panel-button tip-bottom" data-placement="bottom" data-toggle="tooltip" data-original-title="Reset all filters" href="<?php echo UrlHelper::getUrl($controller, $action); ?>">
                            <span class="fa fa-refresh"></span>
                        </a>

						<span class="panel-title">Audit log</span>

					</div>

					<div class="panel-body">

                        {{--@include('templates.search')--}}

						<?php if(count($results)): ?>

						<div class="table-responsive">
							<table class="table table-hover">
								<tr>
									<th>&nbsp;</th>
									<th> <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'object'), true); ?>">Object <?php if ($order_by == 'object'): ?><i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?></a></th>
									<th> <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'object_id'), true); ?>">Object id <?php if ($order_by == 'object_id'): ?><i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?></a></th>
									<th> <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'type'), true); ?>">Type <?php if ($order_by == 'type'): ?><i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?></a></th>
									<th>Created by</th>
									<th> <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'created_at'), true); ?>">Date/Time <?php if ($order_by == 'created_at'): ?><i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?></a></th>
								</tr>

								@foreach ($results as $result)

									<tr>
										<td class="td-one">
											<a class="btn btn-xs btn-primary tip-bottom" data-placement="bottom" data-toggle="tooltip" data-original-title="View" href="<?php echo UrlHelper::getUrl($controller, 'view', $paginationData, array('id' => $result->id)); ?>"><span class="fa fa-eye"></span></a>
										</td>

										<td><span class="bold">{{ $result->object }}</span></td>
										<td>{{ $result->object_id }}</td>
										<td>{{ ucfirst($result->type) }}</td>

										<td>
											<?php $created_by = json_decode($result->created_by, true); ?>

											@if($created_by['user_id'] != null)
												{{ "User: ". $created_by['user_name'] ." (#".$created_by['user_id'].")" }}
											@elseif($created_by['customer_id'] != null)
												{{ "Customer: ". $created_by['customer_name'] ." (#".$created_by['customer_id'].")" }}
											@elseif($user['system'] != null)
												SYSTEM
											@else
												N/A
											@endif
										</td>

										<td>{{ date_format(date_create($result->created_at), 'd-M-Y H:i:s') }}</td>

									</tr>

								@endforeach
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

@endsection