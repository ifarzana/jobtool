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
									<div class="form-group <?php if($errors->has('name')){ echo "has-error";} ?>">

										{!! Form::label('name', 'Domain name*') !!}

										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-globe"></i></span>
											{!! Form::text('name',null,['class'=>'form-control', 'required', $readonly]) !!}
										</div>

									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group <?php if($errors->has('registration_date')){ echo "has-error";} ?>">
										{!! Form::label('registration_date', 'Registration date*') !!}

										<div class="input-group">
											{!! Form::text('registration_date', null,['class'=>'form-control date', 'required', $readonly]) !!}
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group <?php if($errors->has('registration_period')){ echo "has-error";} ?>">
										{!! Form::label('registration_period', 'Registration period*') !!}
										{!! Form::number('registration_period', 1, ['class'=>'form-control', 'required', 'min' => 1, 'max' => 99, 'step' => 1, $readonly]) !!}

									</div>
								</div>

								<div class="col-md-3">
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
										{!! Form::text('cost', null,['class'=>'form-control', 'required', $readonly]) !!}
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
										{!! Form::text('cms', null,['class'=>'form-control', 'required', $readonly]) !!}
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group <?php if($errors->has('ssl')){ echo "has-error";} ?>">
										{!! Form::label('ssl', 'SSL') !!}
										{!! Form::text('ssl', null,['class'=>'form-control', 'required', $readonly]) !!}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<h2 class="form-section-title">Invoice</h2>
									<hr class="text-subline">
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group <?php if($errors->has('invoice_id')){ echo "has-error";} ?>">
										{!! Form::label('invoice_id', 'Filename') !!}
										{!! Form::text('invoice_id', $Invoice ? $Invoice->filename : 'N/A',['class'=>'form-control', 'required', $readonly]) !!}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group <?php if($errors->has('invoice_status_id')){ echo "has-error";} ?>">
										{!! Form::label('invoice_status_id', 'Status') !!}
										{!! Form::text('invoice_status_id', $Invoice ? $Invoice->status->name : 'N/A',['class'=>'form-control', 'required', $readonly]) !!}
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
									<div class="form-group <?php if($no_email_address){ echo "no-email-address";} ?><?php if($errors->has('contact_id')){ echo "has-error";} ?>">
										{!! Form::label('contact_id', 'Notify to') !!}

										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-user"></i></span>
											{!! Form::text('contact_id', $contact,['class'=>'form-control', 'required', $readonly]) !!}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<h2 class="form-section-title">Upcoming renewal</h2>
									<hr class="text-subline">
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group <?php if($errors->has('expiry_date')){ echo "has-error";} ?>">
										{!! Form::label('expiry_date', 'Expiry date') !!}
										{!! Form::text('expiry_date', date_format(date_create($due_date), 'd-M-Y'),['class'=>'form-control', 'required', $readonly]) !!}
									</div>
								</div>
							</div>

							{!! Form::close() !!}

						</div>

					</div>

				</div>
			</div>

			<!-- Domains -->
			<div class="cipanel">
				<div class="cipanel-title">
					<h5 class="text-center">Domain</h5>
				</div>

				<!-- BEGIN  -->
			@if(request()->get('new_renewal'))
				<?php $aa = true; ?>
			@endif

			<!-- END  -->

				<div class="cipanel-content">
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