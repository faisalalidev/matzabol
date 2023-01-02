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
    if (Auth::check()) {
        return redirect('admin/dashboard');
    } else {
        return view('admin.login');
    }
});


Route::get('/resize/{img_dir}/{img}/{h?}/{w?}', function ($img_dir, $img, $h = '', $w = '') {
    try {
        if ($h && $w) {
            return \Image::make(asset("storage/app/$img_dir/$img"))->resize($h, $w)->response('png');
        } else {
            return response(file_get_contents(asset("storage/app/$img_dir/$img")))
                ->header('Content-Type', 'image/png');
        }
    } catch (\Exception $e) {
        return \App\Helpers\RESTAPIHelper::response([], 500, $e->getMessage());
    }
});


/***************************************ADMIN ROUTES*********************************************************/
/***************************************ADMIN ROUTES*********************************************************/
/***************************************ADMIN ROUTES*********************************************************/
/***************************************ADMIN ROUTES*********************************************************/


// *********************Admin Public Routes**************


Route::get('admin', function () {
    if (Auth::check()) {
        return redirect('admin/dashboard');
    } else {
        return redirect('admin/login');
    }
});
Route::get('admin/login', function () {

    if (Auth::check()) {
        return redirect('admin/dashboard');
    } else {
        return view('admin.login');
    }
});
Route::post('admin/login', ['as' => 'login', 'uses' => 'Admin\AdminController@login']);

/******************************************Admin Middleware Routes************************************/

Route::group(['prefix' => 'admin', 'middleware' => 'admin.auth', 'namespace' => 'Admin'], function () {

    Route::get('/logout', function () {

        Auth::logout();
        Session::flush();
        return redirect('admin/login');
    });
    /*Admin Dashboard*/
    Route::get('/dashboard', 'AdminController@index')->name('admin.dashboard');

    /*Users Routes*/
    Route::get('/users-data', 'UserController@getDataTable');
    Route::get('users/{filter?}/{id?}', 'UserController@manageUser')->where(['filter' => '[a-z]+', 'id' => '[0-9]+']);

    /*Interest Routes*/
    Route::get('/interests-data', 'InterestController@getDataTable');
    Route::get('interests/{filter?}/{id?}', 'InterestController@manageInterest')->where(['filter' => '[a-z]+', 'id' => '[0-9]+']);
    Route::post('/interests/store', 'InterestController@store');


    /*Event Routes*/
    Route::get('/events-data', 'EventController@getDataTable');
    Route::get('events/{filter?}/{id?}', 'EventController@manageEvent')->where(['filter' => '[a-z]+', 'id' => '[0-9]+']);
    Route::post('/events/store', 'EventController@store');

//    Route::resource('interest', 'InterestController');
//    Route::resource('users', 'UserController');
    /*Notifications*/
    Route::get('notifications/create', 'NotificationController@create');
    Route::post('notifications/store', 'NotificationController@store');
    Route::get('notifications/users-type/{type}', 'NotificationController@getUsersByType');

    /*CMS page Routes*/
    Route::get('/cmspages-data', 'CmspageController@getDataTable');
    Route::get('cms-page/{filter?}/{id?}', 'CmspageController@manageCmspages')->where(['filter' => '[a-z]+', 'id' => '[0-9]+']);
    Route::post('/cms-page/store', 'CmspageController@store');
    Route::post('/cms-page/update/{id}', 'CmspageController@update');

    /*User Report*/
    Route::get('/user-reports-data', 'UserReportController@getDataTable');
    Route::get('/user-reports', 'UserReportController@index');
    Route::get('/report/delete/{id}', 'UserReportController@delete')->where(['id' => '[0-9]+']);

    /*Admin Panel Settings*/
    Route::get('/admin-settings', 'AdminController@changePassword');
    Route::post('/settings/update', 'AdminController@updatePassword');

    /*Contact US*/
    Route::get('/contact-us-data', 'ContactusController@getDataTable');
    Route::get('/contact-us', 'ContactusController@index');
    Route::get('/contact-us/delete/{id}', 'ContactusController@delete')->where(['id' => '[0-9]+']);

    //Admin Routes
    Route::group(['middleware' => 'AdminRoutes'], function () {
        /*SubAdmin Routes*/
        Route::get('/sub-admin-data', 'SubAdminController@getDataTable');
        Route::get('sub-admin/{filter?}/{id?}', 'SubAdminController@manageEvents')->where(['filter' => '[a-z]+', 'id' => '[0-9]+']);
        Route::post('/sub-admin/store', 'SubAdminController@store');
        Route::post('/sub-admin/update/{id}', 'SubAdminController@update');

        /*Email Templates Routes*/
        Route::get('/email-template-data', 'EmailTemplateController@getDataTable');
        Route::get('email-template/{filter?}/{id?}', 'EmailTemplateController@manageEvents')->where(['filter' => '[a-z]+', 'id' => '[0-9]+']);
        Route::post('/email-template/store', 'EmailTemplateController@store');
        Route::post('/email-template/update/{id}', 'EmailTemplateController@update');
    });

});
