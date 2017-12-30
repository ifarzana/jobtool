@extends('templates.generic')

@section('content')

    <div class="container">
        <div class="row">

            <div class="col-sm-4"></div>

            <div class="col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                        <h2 class="text-center">Restricted Area</h2>
                        <hr/>
                        <a href="/auth/login" class="btn btn-primary" title="Login">Log in</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-4"></div>

        </div>
    </div>

@endsection