<?php

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

Route::get('/', function () {
	return redirect('backend/');
});

Route::match(array('GET','POST'),'/backend/login','Backend\LoginController@index');

/* SUPER ADMIN */
Route::group(array('prefix' => 'backend','middleware'=> ['token_super_admin']), function()
{
	Route::get('/setting','Backend\SettingController@index');
    Route::post('/setting','Backend\SettingController@update');
    
});

/* ADMIN SUPER*/
Route::group(array('prefix' => 'backend','middleware'=> ['token_super']), function()
{
    /* MASTER CUSTOMER OMZET */
    Route::get('/master-omzet/upload','Backend\OmzetController@upload');	
    Route::post('/master-omzet/upload','Backend\OmzetController@upload_store');	
    Route::get('/master-omzet/datatable','Backend\OmzetController@datatable');	
    Route::resource('master-omzet', 'Backend\OmzetController');

    
});


/* ADMIN */
Route::group(array('prefix' => 'backend','middleware'=> ['token_admin']), function()
{
    Route::get('/dashboard','Backend\DashboardController@dashboard');

    /* MASTER CAMPAIGN */
    Route::get('/campaign/datatable','Backend\CampaignController@datatable');	
    Route::resource('campaign', 'Backend\CampaignController');

    
});


/* ADMIN DAN USER*/
Route::group(array('prefix' => 'backend','middleware'=> ['token_user']), function()
{
    Route::get('',function (){return Redirect::to('backend/dashboard');});
	
        
});

