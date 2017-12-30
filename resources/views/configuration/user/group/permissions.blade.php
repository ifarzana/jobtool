<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path() . '/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/
?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>User group permissions</h2>
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
								   data-original-title="Back" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData); ?>">
									<span class="fa fa-arrow-left"></span>
								</a>
							</div>
						</div>
						<div class="cipanel-content">

							<?php if(count($resources)): ?>

								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Resource name</th>
												<th>Route</th>
												<th>Allowed</th>
												<th>Privileges</th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
											</tr>
										</thead>

										<tbody>

											@foreach ($resources as $resource)

												<tr>

													<td><span class="bold">{{ $resource['name'] }}</span></td>

													<td>{{ '/' . $resource['route'] }}</td>

													<td>
														@if($resource['allowed'] == true)
															<span class="bold-no-colour text-success">Yes</span>
														@else
															<span class="bold-no-colour text-danger">No</span>
														@endif
													</td>

													<td>
														@if(isset($permissionsArray[$resource['id']]))

															@if( count($PrivilegeAvailabilityArray[$resource['id']]) == count($permissionsArray[$resource['id']]) )
																<span class="text-success">All privileges</span>
															@else
																{{ implode(',', $permissionsArray[$resource['id']]) }}
															@endif
														@else
															<span class="text-danger">None</span>
														@endif
													</td>

													<td>
														@if($resource['allowed'] == true)
															<a class="btn btn-xs btn-danger permission-button" href="<?php echo UrlHelper::getUrl($controller, 'changeResource', $paginationData, array('group_id' => $group->id, 'resource_id' => $resource['id'])); ?>"><span class="fa fa-minus-circle"></span> Disallow</a>
														@else
															<a class="btn btn-xs btn-success permission-button" href="<?php echo UrlHelper::getUrl($controller, 'changeResource', $paginationData, array('group_id' => $group->id, 'resource_id' => $resource['id'])); ?>"><span class="fa fa-check-circle"></span> Allow</a>
														@endif
													</td>

													<td>
														<?php
														if( ($resource['allowed'] == false) OR ($resource['default'] == 1) ) {
															$disabled = 'disabled';
														}else {
															$disabled = '';
														}
														?>

														<div class="btn-group dropup">
															<button type="button" class="btn btn-xs btn-default dropdown-toggle" {{ $disabled }} data-toggle="dropdown" aria-expanded="false">
																Change privilege <span class="caret"></span>
															</button>
															<ul class="dropdown-menu" role="menu">

																@if(isset($PrivilegeAvailabilityArray[$resource['id']]))

																	@foreach($AllPrivilegesArray as $privilege_id => $privilege)

																		@if(isset($PrivilegeAvailabilityArray[$resource['id']][$privilege_id]))

																			<li class="text-left">
																				<a href="<?php echo UrlHelper::getUrl($controller, 'changePermission', $paginationData, array('group_id' => $group->id, 'resource_id' => $resource['id'], 'privilege_id' => $privilege_id)); ?>">
																					<i class="{{ $AllPrivilegesArray[$privilege_id]['icon'] }}"></i> {{ ucfirst($AllPrivilegesArray[$privilege_id]['name']) }}
																				</a>
																			</li>

																		@endif

																	@endforeach

																@endif

															</ul>
														</div>

													</td>

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

@endsection
