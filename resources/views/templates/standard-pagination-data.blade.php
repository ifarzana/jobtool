<?php
/*BEGIN ROUTING VARIABLES*/
$routeDetails = \App\Helpers\UrlHelper::getRouteDetails();
$controller = $routeDetails['controller'];
$action = $routeDetails['action'];
/*END ROUTING VARIABLES*/

/*BEGIN PAGINATION & SEARCH*/
$order_by = isset($paginationData['order_by']) ? $paginationData['order_by'] : 'id' ;
$order = isset($paginationData['order']) ? $paginationData['order']: 'ASC';
$p = isset($paginationData['p']) ? $paginationData['p'] : 1;
$paginated = isset($paginationData['paginated']) ? $paginationData['paginated'] : false;

$url_order = 'ASC';

if(isset($paginationData['order'])) {
    $url_order = $paginationData['order'] == 'ASC' ? 'DESC' : 'ASC';
}
/*END PAGINATION & SEARCH*/

/*BEGIN READONLY*/
$readonly = '';
/*EDN READONLY*/