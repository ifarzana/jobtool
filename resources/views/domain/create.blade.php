<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>New domain registration</h2>
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

									{!! Form::open(['method' => 'POST', 'url' => UrlHelper::getUrl($controller, $action, $paginationData)]) !!}

									{!! Form::hidden('id', null) !!}
									{!! Form::hidden('client_id', null) !!}
									{!! Form::hidden('currency_id', $currency->id) !!}

									<div class="row">
										<div class="col-md-2"></div>

										<div class="col-md-8">
											<div class="form-group <?php if($errors->has('client')){ echo "has-error";} ?>">
												{!! Form::label('client', 'Client name*') !!}

												<div class="input-group form-input-group domain-client-search-fg">
													{!! Form::text('client', null, ['class' => 'form-control typeahead', 'required', $readonly, 'placeholder' => 'Search for a client...']) !!}
													<span class="input-group-addon">
														<button type="button" id="clear-client-btn" class="btn btn-primary tip-left" data-placement="left" data-toggle="tooltip" data-original-title="Clear"><i class="fa fa-times-circle"></i></button>
													</span>
												</div>
											</div>
										</div>

										<div class="col-md-2"></div>
									</div>


									<div id="contact_fg">
										<div class="row">

											<div class="col-md-2"></div>

											<div class="col-md-8">
												<div class="form-group <?php if($errors->has('contact_id')){ echo "has-error";} ?>">
													{!! Form::label('contact_id', 'Contact') !!}
													{!! Form::select('contact_id', [], null, ['class'=>'form-control', $readonly]) !!}
												</div>
											</div>

											<div class="col-md-2"></div>

										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<h2 class="form-section-title">Summary</h2>
											<hr class="text-subline">
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group <?php if($errors->has('name')){ echo "has-error";} ?>">

												{!! Form::label('name', 'Name*') !!}
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-globe"></i></span>
													{!! Form::text('name',null,['class'=>'form-control', 'required', $readonly]) !!}
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-4">
											<div class="form-group <?php if($errors->has('registration_date')){ echo "has-error";} ?>">
												{!! Form::label('registration_date', 'Registration date*') !!}

												<div class="input-group">
													{!! Form::text('registration_date', null,['class'=>'form-control date', 'required', $readonly]) !!}
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group <?php if($errors->has('registration_period')){ echo "has-error";} ?>">
												{!! Form::label('registration_period', 'Registration period*') !!}
												{!! Form::number('registration_period', 1, ['class'=>'form-control', 'required', 'min' => 1, 'max' => 99, 'step' => 1, $readonly]) !!}

											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group <?php if($errors->has('interval_id')){ echo "has-error";} ?>">
												{!! Form::label('interval_id', 'Interval*') !!}
												{!! Form::select('interval_id', [null => 'Please choose'] + $intervals, null,['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>

									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('cost')){ echo "has-error";} ?>">
												{!! Form::label('cost', 'Cost*') !!}

												<div class="input-group">
													<span class="input-group-addon">{{$currency->symbol}}</span>
													{!! Form::text('cost', null,['class'=>'form-control', 'required', $readonly]) !!}
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('status_id')){ echo "has-error";} ?>">
												{!! Form::label('status_id', 'Active*') !!}
												{!! Form::select('status_id', [2 => 'No', 1 => 'Yes'], 1,['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<h2 class="form-section-title">Other</h2>
											<hr class="text-subline">
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('registrar_id')){ echo "has-error";} ?>">
												{!! Form::label('registrar_id', 'Registrar') !!}
												{!! Form::select('registrar_id', [null => 'Please choose'] + $registrars, null, ['class'=>'form-control',  $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('server_id')){ echo "has-error";} ?>">
												{!! Form::label('server_id', 'Server') !!}
												{!! Form::select('server_id', [null => 'Please choose'] + $servers, null, ['class'=>'form-control',  $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('cms')){ echo "has-error";} ?>">
												{!! Form::label('cms', 'CMS') !!}
												{!! Form::text('cms', null,['class'=>'form-control', $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('ssl')){ echo "has-error";} ?>">
												{!! Form::label('ssl', 'SSL Certificate') !!}
												{!! Form::text('ssl', null,['class'=>'form-control', $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row client-selected-div">
										<div class="col-xs-12 text-right">
											{!! Form::submit('Save', ['id' => 'save-btn', 'class' => 'btn btn-primary']) !!}

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

	<script>
		$('.form-control.date').datepicker({
			format: "dd-M-yyyy",
			todayBtn: false,
			autoclose: true,
			todayHighlight: true
		});
	</script>

	<script type="text/javascript" src="{!! asset('js/client/client.min.js') !!}"></script>

@endsection