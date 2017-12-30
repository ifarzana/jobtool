<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>Edit domain settings</h2>
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
										<div class="col-md-12">
											<div class="form-group <?php if($errors->has('days_before_renewal_due_reminder')){ echo "has-error";} ?>">
												{!! Form::label('days_before_renewal_due_reminder', 'Days before renewal due reminder*') !!}
												{!! Form::text('days_before_renewal_due_reminder',null,['class'=>'form-control', 'required', $readonly]) !!}
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
    </script>

@endsection