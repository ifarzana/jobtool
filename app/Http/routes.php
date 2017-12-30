<?php

/*Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index');*/

Route::get('/',
    [
        'uses' => 'Index\IndexController@index',
        'middleware' => ['auth']
    ]
);

/*Dashboard*/
Route::get('home', [
        'as' => 'home',
        'uses' => 'HomeController@index',
        'middleware' => ['auth', 'acl']
    ]
);

/*Auth*/
Route::group(array('prefix' => 'auth'), function()
{

    Route::get('/',
        [
            'uses' => 'Auth\AuthController@index'
        ]
    );

    Route::match(array('GET', 'POST'), 'login',
        [
            'uses' => 'Auth\AuthController@login'
        ]
    );

    Route::get('logout',
        [
            'uses' => 'Auth\AuthController@logout'
        ]
    );

    Route::get('restricted-area',
        [
            'uses' => 'Auth\AuthController@restrictedArea'
        ]
    );

    Route::get('no-permission',
        [
            'uses' => 'Auth\AuthController@noPermission'
        ]
    );

    Route::get('suspended-account',
        [
            'uses' => 'Auth\AuthController@suspendedAccount'
        ]
    );

    Route::get('error',
        [
            'uses' => 'Auth\AuthController@error'
        ]
    );

});

/*Search*/
Route::post('search',
    [
        'as' => 'search',
        'uses' => 'Search\SearchController@index',
        'middleware' => ['auth']
    ]
);

/*Projects*/
Route::group(array('prefix' => 'projects', 'as' => 'projects'), function()
{

    Route::get('/',
        [
            'uses' => 'Project\ProjectController@index',
            'middleware' => ['auth', 'acl']
        ]
    );

});

/*Tasks*/
include_once('Routes/tasks.php');

/*Alerts*/
include_once('Routes/alerts.php');

/*Clients*/
include_once('Routes/clients.php');

/*Domains*/
include_once('Routes/domains.php');

/*Configuration*/
include_once('Routes/configuration.php');

/*API*/
include_once('Routes/api.php');

/*Invoices*/
include_once('Routes/invoices.php');