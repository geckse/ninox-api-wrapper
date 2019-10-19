<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/*
  Index
*/
$router->get('/', 'NinoxController@handleRoot');

/*
  Tables
*/
$router->get('/'.env('URL_PART_TABLES', 'tables').'/', 'NinoxController@handleTables');
$router->get('/'.env('URL_PART_TABLES', 'tables').'/{id}', 'NinoxController@handleTable');

/*
  Records
*/
$router->get('/'.env('URL_PART_TABLES', 'tables').'/{id}/'.env('URL_PART_RECORDS', 'records'), 'NinoxController@handleTablesRecords');
$router->get('/'.env('URL_PART_TABLES', 'tables').'/{id}/'.env('URL_PART_RECORDS', 'records').'/{recordId}/', 'NinoxController@handleTablesRecordSingle');
