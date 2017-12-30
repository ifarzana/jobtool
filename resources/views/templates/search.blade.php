<?php

use App\Helpers\UrlHelper;

/*BEGIN ROUTING VARIABLES*/
$routeDetails = UrlHelper::getRouteDetails();
$controller = $routeDetails['controller'];
$action = $routeDetails['action'];
/*END ROUTING VARIABLES*/

$form_action = 'Search\SearchController'. '@' .'index';

$search_by = request()->get('search_by') ? request()->get('search_by') : null;

?>

{!! Form::open(array('action' => $form_action, 'method' => 'post', 'id' => 'SearchForm', 'class' => 'form text-center search-form')) !!}

{{ Form::hidden('controller', $controller) }}
{{ Form::hidden('action', $action) }}

@foreach (UrlHelper::getKeysToInclude() as $key)
    @if(isset($routeDetails[$key]))
        {{ Form::hidden($key, $routeDetails[$key]) }}
    @endif
@endforeach

<div class="col-lg-4"></div>

<div class="input-group col-lg-4">

    {!! Form::text('search', urldecode($search_by), array('required' => 'required', 'class' => 'form-control', 'placeholder' => 'Search')) !!}

<div class="input-group-btn">
    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> </button>
</div>

</div>

<div class="col-lg-4"></div>

{!! Form::close() !!}