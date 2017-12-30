@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Domains expiring in next 30 days</h2>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">

                    <div class="cipanel">

                        <div class="cipanel-title">
                            <div class="cipanel-tools">

                                <a href="<?php echo UrlHelper::getUrl('HomeController', 'index', array()); ?>"
                                   class="btn btn-primary btn-xs">Close</a>
                            </div>
                        </div>

                        <div class="cipanel-content">

                            <?php if(count($results)): ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Due date</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($results as $result)

                                        <tr>
                                            <td>
                                                <strong>
                                                    <a href="<?php echo UrlHelper::getUrl('Domain\DomainController', 'view', array(), array('id' => $result->id)); ?>">{{ $result->name }}</a>
                                                </strong>
                                                <p>
                                                    <small> Created at: {{date_format(date_create($result->created_at), 'd-M-Y H:i')}}</small>
                                                </p>
                                            </td>

                                            <td>{{$result->client->name}}</td>
                                            <td>{{$result->status->name}}</td>
                                            <td><h4 class="text-date">{{ date_format(date_create($result->expiry_date), 'd-M-Y')  }}</h4></td>

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