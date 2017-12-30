<?php
/*BEGIN ROUTING VARIABLES*/
$routeDetails = UrlHelper::getRouteDetails();
$controller = $routeDetails['controller'];
$action = $routeDetails['action'];
/*END ROUTING VARIABLES*/

/*BEGIN PAGINATION & SEARCH*/
$order_by = $paginationData['order_by'];
$order = $paginationData['order'];
$p = $paginationData['p'];
$paginated = $paginationData['paginated'];

$url_order = $paginationData['order'] == 'ASC' ? 'DESC' : 'ASC';
/*END PAGINATION & SEARCH*/
?>

@extends('templates.default')

@section('content')



@endsection