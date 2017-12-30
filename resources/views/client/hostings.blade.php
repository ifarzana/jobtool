<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>Hostings</h2>
			<strong>Client name:</strong> {{ $client->name }}
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
								   data-original-title="Back" href="<?php echo UrlHelper::getUrl($controller, 'index', array()); ?>">
									<span class="fa fa-arrow-left"></span>
								</a>

								<a href="<?php echo UrlHelper::getUrl($controller, $action, array('id' => $client->id)); ?>"
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
										<th> <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'name'), true); ?>">Name <?php if ($order_by == 'name'): ?><i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?></a></th>
										<th> <a href="<?php echo UrlHelper::getUrl($controller, $action, $paginationData, array('order_by' => 'created_at'), true); ?>">Date / Time <?php if ($order_by == 'created_at'): ?><i class="fa fa-caret-<?php echo $url_order == 'ASC' ? 'down' : 'up' ?>"></i><?php endif; ?></a></th>
									</tr>
									</thead>

									<tbody>
										@foreach ($results as $result)

											<tr>
												<td><span class="bold">{{ $result->name }}</span></td>

												<td>{{date_format(date_create($result->created_at), 'd-M-Y H:i')}}</td>

											</tr>

										@endforeach
									</tbody>
								</table>
							</div>



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