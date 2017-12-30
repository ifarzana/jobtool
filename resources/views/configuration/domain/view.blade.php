<?php
/*BEGIN STANDARD PAGINATION DATA*/
include_once(base_path().'/resources/views/templates/standard-pagination-data.blade.php');
/*END STANDARD PAGINATION DATA*/

$readonly = 'readonly';

?>

@extends('templates.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>Domain settings</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="container content">
			<div class="row">
				<div class="col-lg-12">

					<div class="cipanel">

						<div class="cipanel-title">
							<div class="cipanel-tools">
								<?php if(AclManagerHelper::hasPermission('update')): ?>
									<a href="<?php echo UrlHelper::getUrl($controller, 'edit', array(), array('id' => $result->id)); ?>"
									   class="btn btn-primary btn-xs">Edit</a>
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

									{!! Form::model($result) !!}

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

									{!! Form::close() !!}

								</div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection