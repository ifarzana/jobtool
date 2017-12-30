<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

$readonly = 'readonly';
?>

@extends('templates.user')

@section('content')

	<div class="container content">

		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-default">

					<div class="panel-heading clearfix">

						<a class="panel-button panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip" data-original-title="Back" href="<?php echo UrlHelper::getUrl($controller, 'index', $paginationData, array('id' => $result->id)); ?>">
							<span class="fa fa-arrow-left"></span>
						</a>

						<a class="panel-button panel-close tip-bottom" data-placement="bottom" data-toggle="tooltip" data-original-title="Close" href={{url('/dashboard')}}>
							<span class="fa fa-times"></span>
						</a>

						<span class="panel-title">View audit log</span>

					</div>

					<div class="panel-body">

						<div class="table-responsive">
							<table class="table table-condensed">

								<tr>
									<td style="border-top: none;"><h5 class="bold-no-colour text-primary">Object</h5></td>
									<td style="border-top: none;"><span class="bold">{{ $result->object }}</span></td>
								</tr>

								<tr>
									<td><h5 class="bold-no-colour text-primary">Object id</h5></td>
									<td>{{ $result->object_id }}</td>
								</tr>

								<tr>
									<td><h5 class="bold-no-colour text-primary">Created by</h5></td>

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

								</tr>

								<tr>
									<td><h5 class="bold-no-colour text-primary">Type</h5></td>
									<td>{{ ucfirst($result->type) }}</td>
								</tr>

								<tr>
									<td><h5 class="bold-no-colour text-primary">Date/Time</h5></td>
									<td>{{ date_format(date_create($result->created_at), 'd-M-Y H:i:s') }}</td>
								</tr>

							</table>
						</div>

						@if($result->type != 'create')

							<div class="table-responsive">

							<table class="table table-bordered">

								<tr>
									<th style="background-color: #3c763d !important; color: #FFFFFF !important;" colspan="2" class="text-center">@if($result->type == 'update') ORIGINAL DATA @else DATA @endif</th>
								</tr>

								@if(!empty($data['original_data']))

									<tr>
										<th>Column</th>
										<th>Value</th>
									</tr>

									@foreach($data['original_data'] as $column => $value)

										<tr class="@if(array_key_exists($column, $changes1)) danger-tr @endif">
											<td>{{ strtoupper(str_replace("_", " ", $column)) }}</td>
											<td>{{ $value }}</td>
										</tr>

									@endforeach

								@else
									<tr>
										<td colspan="2" class="text-center">No data</td>
									</tr>
								@endif

							</table>

						</div>

						@endif

						@if($result->type != 'delete')

							<div class="table-responsive">

							<table class="table table-bordered">

								<tr>
									<th style="background-color: #8a6d3b !important; color: #FFFFFF !important;" colspan="2" class="text-center">@if($result->type != 'create') UPDATED DATA @else DATA @endif</th>
								</tr>

								@if(!empty($data['updated_data']))

									<tr>
										<th>Column</th>
										<th>Value</th>
									</tr>

									@foreach($data['updated_data'] as $column => $value)

										<tr class="@if(array_key_exists($column, $changes2)) danger-tr @endif">
											<td>{{ strtoupper(str_replace("_", " ", $column)) }}</td>
											<td>{{ $value }}</td>
										</tr>

									@endforeach

								@else
									<tr>
										<td colspan="2" class="text-center">No data</td>
									</tr>
								@endif

							</table>

						</div>

						@endif

					</div>


				</div>
			</div>


		</div>

	</div>

@endsection