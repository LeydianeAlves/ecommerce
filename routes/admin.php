<?php

Route::group(['prefix'  =>  'admin'], function () {

Route::get('login', 'Admin\LoginController@showLoginForm')->name('admin.login');
Route::post('login', 'Admin\LoginController@login')->name('admin.login.post');
Route::get('logout', 'Admin\LoginController@logout')->name('admin.logout');
// Route::get('/', function () {
//    return view('admin.dashboard.index');
// });
// });

Route::group(['middleware' => ['auth:admin']], function() {
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');

    Route::get('/settings', 'Admin\SettingsController@index')->name('admin.settings');
    Route::post('/settings', 'Admin\SettingsController@update')->name('admin.settings.update');

    Route::group(['prefix' => 'categories'], function () {

        Route::get('/', 'Admin\CategoryController@index')->name('admin.categories.index');
        Route::get('/create', 'Admin\CategoryController@create')->name('admin.categories.create');
        Route::post('/store', 'Admin\CategoryController@store')->name('admin.categories.store');
        Route::get('/{id}/edit', 'Admin\CategoryController@edit')->name('admin.categories.edit');
        Route::post('/update', 'Admin\CategoryController@update')->name('admin.categories.update');
        Route::get('/{id}/delete', 'Admin\CategoryController@delete')->name('admin.categories.delete');
    });
});
});