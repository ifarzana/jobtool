@extends('templates.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Dashboard</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            @foreach($results as $result)

                                <div class="file-box">
                                    <div class="file" style="background-color: #317589">
                                        <a href="{{ '/'.$result->route }}" title="{{ $result->label }}" class="loading">
                                            <div class="icon">
                                                <i class="{{ $result->icon }}"></i>
                                            </div>
                                            <div class="file-name text-center">
                                                {{ $result->label }}
                                            </div>

                                        </a>
                                    </div>
                                </div>

                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function(){
            $('.file-box').each(function() {
                animationHover(this, 'pulse');
            });
        });
    </script>
@endsection