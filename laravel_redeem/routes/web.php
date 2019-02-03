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
//logout
Route::get('/logout','Backend\LoginController@logout');


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
    Route::get('/campaign/{id}/edit-list-hadiah','Backend\CampaignController@edit_list_hadiah');
    Route::match(array('PUT','PATCH'),'/campaign/{id}/edit-list-hadiah','Backend\CampaignController@update_list_hadiah');
    Route::get('/campaign/{id}/edit-pembagian-hadiah','Backend\CampaignController@edit_pembagian_hadiah');
    Route::post('/campaign/{id}/edit-pembagian-hadiah','Backend\CampaignController@update_pembagian_hadiah');
    Route::get('/campaign/{id}/edit-master-emas','Backend\CampaignController@edit_master_emas');
    Route::post('/campaign/{id}/edit-master-emas','Backend\CampaignController@update_master_emas');
    Route::resource('campaign', 'Backend\CampaignController');
    
});


/* ADMIN DAN USER*/
Route::group(array('prefix' => 'backend','middleware'=> ['token_all']), function()
{
    Route::get('',function (){return Redirect::to('backend/dashboard');});
});

/* USER AJA */
Route::group(array('prefix' => 'backend','middleware'=> ['token_user']), function()
{
    Route::get('/redeem-hadiah/datatable','Backend\RedeemController@datatable');
    Route::get('/redeem-hadiah','Backend\RedeemController@index');

    Route::get('/redeem-hadiah/{id}/klaim-hadiah','Backend\RedeemController@klaim_hadiah');
    Route::post('/redeem-hadiah/{id}/klaim-hadiah','Backend\RedeemController@klaim_hadiah_update');

    Route::get('/redeem-hadiah/{id}/konvert-emas','Backend\RedeemController@konversi_emas');
    Route::post('/redeem-hadiah/{id}/konvert-emas','Backend\RedeemController@konversi_emas_update');
});