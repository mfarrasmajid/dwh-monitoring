<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', 'AuthController@login')->name('login');
Route::post('/login', 'AuthController@submit_login');
Route::get('/logout', 'AuthController@logout')->name('logout');
Route::get('/', 'MainController@portal')->name('portal');

Route::get('/init_send_credentials_dashboard', 'MainController@init_send_credentials_dashboard');
Route::get('/test_wa_notif', 'MainController@test_wa_notif');
Route::get('/get_ip', 'MainController@get_ip');
Route::prefix('ai')->group(function () {
    Route::get('/dashboard', 'AiQueryController@dashboard_aiagent')->name('dashboard_aiagent');
    Route::post('/stream', 'AiQueryController@stream');
});
// Route::prefix('helpdesk')->group(function () {
//     Route::get('/dashboard', 'HelpdeskController@dashboard_portal')->name('dashboard_portal');
//     Route::get('/trouble_ticket', 'HelpdeskController@dashboard_trouble_ticket')->name('dashboard_trouble_ticket');
//     Route::get('/maintenance_order', 'HelpdeskController@dashboard_maintenance_order')->name('dashboard_maintenance_order');
// });
Route::prefix('dwh')->group(function () {
    Route::get('/dashboard', 'DWHController@dashboard_dwh')->name('dashboard_dwh');
    Route::get('/manage_airflow_logs', 'DWHController@manage_airflow_logs')->name('manage_airflow_logs');
    Route::get('/status_airflow', 'DWHController@status_airflow')->name('status_airflow');
    Route::get('/detail_airflow_logs/{dag_name}', 'DWHController@detail_airflow_logs')->name('detail_airflow_logs');
    Route::get('/detail_airflow_logs_id/{mark}', 'DWHController@detail_airflow_logs_id')->name('detail_airflow_logs_id');
});
Route::prefix('admin')->group(function () {
    Route::get('/dashboard_admin', 'AdminController@dashboard_admin')->name('dashboard_admin');
    Route::get('/manage_users', 'AdminController@manage_users')->name('manage_users');
    Route::get('/detail_users/{id?}', 'AdminController@detail_users')->name('detail_users');
    Route::post('/detail_users/{id?}', 'AdminController@submit_users');
    Route::get('/manage_airflow_table', 'AdminController@manage_airflow_table')->name('manage_airflow_table');
    Route::get('/detail_airflow_table/{id?}', 'AdminController@detail_airflow_table')->name('detail_airflow_table');
    Route::post('/detail_airflow_table/{id?}', 'AdminController@submit_airflow_table');
    Route::get('/ingestion_registry',               'AdminController@index_datalake');
    Route::get('/ingestion_registry/create',        'AdminController@create_datalake');
    Route::post('/ingestion_registry',              'AdminController@store_datalake');
    Route::get('/ingestion_registry/{id}/edit',     'AdminController@edit_datalake');
    Route::put('/ingestion_registry/{id}',          'AdminController@update_datalake');
    Route::delete('/ingestion_registry/{id}',       'AdminController@destroy_datalake');
    Route::patch('/ingestion_registry/{id}/toggle', 'AdminController@toggle_datalake');
});
Route::prefix('api')->group(function() {
    // Route::prefix('helpdesk')->group(function () {
    //     Route::get('/get_list_trouble_ticket', 'ApiController@get_list_trouble_ticket');
    //     Route::post('/export_trouble_ticket', 'ApiController@export_trouble_ticket');
    //     Route::get('/get_list_maintenance_order', 'ApiController@get_list_maintenance_order');
    //     Route::post('/export_maintenance_order', 'ApiController@export_maintenance_order');
    // });
    Route::prefix('dwh')->group(function () {
        Route::post('/get_list_airflow_logs/{kategori}', 'DWHController@get_list_airflow_logs');
        Route::post('/get_list_airflow_logs_detail/{dag_name}', 'DWHController@get_list_airflow_logs_detail');
        Route::post('/get_list_airflow_logs_detail_id/{mark}', 'DWHController@get_list_airflow_logs_detail_id');
    });
    Route::prefix('admin')->group(function(){
        Route::post('/delete_users/{id}', 'AdminController@delete_users')->name('delete_users');
        Route::post('/get_list_manage_users', 'AdminController@get_list_manage_users')->name('get_list_manage_users');
        Route::post('/delete_airflow_table/{id}', 'AdminController@delete_airflow_table')->name('delete_airflow_table');
        Route::post('/get_list_manage_airflow_table', 'AdminController@get_list_manage_airflow_table')->name('get_list_manage_airflow_table');
    });
});
