<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>New user</h2>
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

									<div class="row">
										<div class="col-md-12">
											<h2 class="form-section-title">Summary</h2>
                                            <hr class="text-subline">
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('name')){ echo "has-error";} ?>">
												{!! Form::label('name', 'Name') !!}
												{!! Form::text('name',null,['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('gender')){ echo "has-error";} ?>">
												{!! Form::label('gender', 'Gender') !!}
												{!! Form::select('gender', [null => 'Please choose'] + ['Male' => 'Male', 'Female' => 'Female'], null, ['class' => 'form-control', $readonly]) !!}
											</div>
										</div>

									</div>

									<div class="row">

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('dob')){ echo "has-error";} ?>">
												{!! Form::label('dob', 'Date of birth') !!}

												<div class="input-group">
													{!! Form::text('dob',null,['class'=>'form-control date', $readonly]) !!}
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('ip_address')){ echo "has-error";} ?>">
												{!! Form::label('ip_address', 'IP address') !!}
												{!! Form::text('ip_address',null,['class'=>'form-control', $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<h2 class="form-section-title">Login details</h2>
                                            <hr class="text-subline">
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('email')){ echo "has-error";} ?>">
												{!! Form::label('email', 'Email address*') !!}
												{!! Form::email('email',null,['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('password')){ echo "has-error";} ?>">
												{!! Form::label('password', 'Password') !!}
												{!! Form::password('password', ['class'=>'form-control', 'placeholder' => 'Leave blank to keep unchanged', $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('group_id')){ echo "has-error";} ?>">
												{!! Form::label('group_id', 'User group*') !!}
												{!! Form::select('group_id', [null => 'Please choose'] + $groups, null, ['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('isActive')){ echo "has-error";} ?>">
												{!! Form::label('isActive', 'Active*') !!}
												{!! Form::select('isActive',[null => 'Please choose'] + [0 => 'No', 1 => 'Yes'], null,['class'=>'form-control', 'required', $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<h2 class="form-section-title">Contact</h2>
                                            <hr class="text-subline">
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group <?php if($errors->has('address')){ echo "has-error";} ?>">
												{!! Form::label('address', 'Address') !!}
												{!! Form::textarea('address',null,['class'=>'form-control', $readonly]) !!}
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('home_phone_number')){ echo "has-error";} ?>">
												{!! Form::label('home_phone_number', 'Home phone number') !!}
												{!! Form::text('home_phone_number',null,['class'=>'form-control', $readonly]) !!}
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('mobile_phone_number')){ echo "has-error";} ?>">
												{!! Form::label('mobile_phone_number', 'Mobile phone number') !!}
												{!! Form::text('mobile_phone_number',null,['class'=>'form-control', $readonly]) !!}
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
											<div class="form-group <?php if($errors->has('joined_at')){ echo "has-error";} ?>">
												{!! Form::label('joined_at', 'Joined at') !!}

												<div class="input-group">
													{!! Form::text('joined_at',null,['class'=>'form-control date', $readonly]) !!}
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group <?php if($errors->has('left_at')){ echo "has-error";} ?>">
												{!! Form::label('left_at', 'Left at') !!}

												<div class="input-group">
													{!! Form::text('left_at',null,['class'=>'form-control date', $readonly]) !!}
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>


											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group <?php if($errors->has('colour')){ echo "has-error";} ?>">
												{!! Form::label('colour', 'Colour') !!}

												<div class="input-group colour">
													{!! Form::text('colour',null,['class'=>'form-control', $readonly]) !!}
													<span class="input-group-addon selected-colour"><i class="fa fa-eyedropper"></i></span>
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

	<script>
		$('.form-control.date').datepicker({
			format: "dd-M-yyyy",
			todayBtn: false,
			autoclose: true,
			todayHighlight: true
		});

		$(function(){
			$('.colour').colorpicker().on('changeColor.colorpicker', function(event){
				$(this).find('.selected-colour')[0].style.backgroundColor = event.color.toHex();
			});

		});
	</script>

@endsection