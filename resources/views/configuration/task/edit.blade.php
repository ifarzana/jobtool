<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>Edit task settings</h2>
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
								   data-original-title="Back" href="<?php echo UrlHelper::getUrl($controller, 'index', array('id' => $result->id)); ?>">
									<span class="fa fa-arrow-left"></span>
								</a>
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

									{!! Form::model($result, ['method' => 'POST', 'url' => UrlHelper::getUrl($controller, $action, array('id' => $result->id))]) !!}

									{!! Form::hidden('id', null) !!}

									<div class="row">
										<div class="col-md-12">
											<h2 class="form-section-title">Summary</h2>
											<hr class="text-subline">
										</div>
									</div>

									<div class="row">

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('schedule_maximum_days_range')){ echo "has-error";} ?>">
												{!! Form::label('schedule_maximum_days_range', 'Maximum number of days to display*') !!}
												{!! Form::text('schedule_maximum_days_range', null, ['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('schedule_refresh_interval')){ echo "has-error";} ?>">
												{!! Form::label('schedule_refresh_interval', 'Refresh interval (seconds)*') !!}
												{!! Form::number('schedule_refresh_interval', null, ['class'=>'form-control', 'required', 'min' => 60, 'max' => 86400, 'step' => 1, $readonly]) !!}
											</div>
										</div>

									</div>

									<div class="row">

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('schedule_enable_drag_and_drop')){ echo "has-error";} ?>">
												{!! Form::label('schedule_enable_drag_and_drop', 'Enable drag & drop*') !!}
												{!! Form::select('schedule_enable_drag_and_drop', [null => 'Please choose', 0 => 'No', 1 => 'Yes'], null,['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('schedule_enable_context_menu')){ echo "has-error";} ?>">
												{!! Form::label('schedule_enable_context_menu', 'Enable context menu*') !!}
												{!! Form::select('schedule_enable_context_menu', [null => 'Please choose', 0 => 'No', 1 => 'Yes'], null,['id' => 'schedule_enable_context_menu', 'class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>

									</div>

									<div id="context-menu-items-fg" class="row">

										<div class="col-md-12">
											<div class="form-group<?php if($errors->has('schedule_context_menu_items')){ echo "has-error";} ?>">
												{!! Form::label('schedule_context_menu_items', 'Context menu items*') !!}
												{!! Form::select('schedule_context_menu_items[]', $context_menu_items, json_decode($result->schedule_context_menu_items), ['id' => 'schedule_context_menu_items', 'class'=>'form-control', 'data-placeholder' => 'Please choose', 'multiple', $readonly]) !!}
											</div>
										</div>

									</div>

									<div class="row">

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('schedule_weekend_days')){ echo "has-error";} ?>">
												{!! Form::label('schedule_weekend_days', 'Weekend days') !!}
												{!! Form::select('schedule_weekend_days[]', $weekend_days, json_decode($result->schedule_weekend_days), ['class'=>'form-control', 'data-placeholder' => 'Please choose', 'multiple', $readonly]) !!}
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('schedule_business_hours')){ echo "has-error";} ?>">
												{!! Form::label('schedule_business_hours', 'Business hours') !!}

												<div class="input-group">
													{!! Form::select('schedule_business_hours_from_time[]', $business_hours, json_decode($result->schedule_business_hours_from_time), ['class'=>'form-control', 'data-placeholder' => 'Please choose', 'single', $readonly]) !!}
													<span class="input-group-addon">to</span>

													{!! Form::select('schedule_business_hours_to_time[]', $business_hours, json_decode($result->schedule_business_hours_to_time), ['class'=>'form-control', 'data-placeholder' => 'Please choose', 'single', $readonly]) !!}
												</div>
											</div>
										</div>

									</div>

									<div class="row">
										<div class="col-xs-12 text-right">
											<button class="btn btn-primary" type="submit">Save</button>
										</div>
									</div>

									{!! Form::close() !!}

								</div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="{!! asset('js/task-settings/task-settings.min.js') !!}"></script>


@endsection