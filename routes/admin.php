<?php
/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['jwt.auth', 'role:superadmin|admin'])->group(function () {
    Route::get('session', 'AuthController@userSession');

    Route::get('clients', 'Admin\ClientController@index')->name('admin.clients.index');
    Route::post('clients', 'Admin\ClientController@store')->name('admin.clients.store')->middleware(['can:create,App\Client']);
    Route::get('clients/{client}', 'Admin\ClientController@show')->name('admin.clients.show')->middleware(['can:view,client']);
    Route::patch('clients/{client}', 'Admin\ClientController@update')->name('admin.clients.update')->middleware(['can:update,client']);
    Route::delete('clients/{client}', 'Admin\ClientController@destroy')->name('admin.clients.destroy')->middleware(['can:delete,client']);

    Route::get('financial-report', 'Admin\ClientController@getFinancialReport')->name('admin.clients.report');

    Route::get('users', 'Admin\MobileUserController@index')->name('admin.users.index');
    Route::post('mobileUsers', 'Admin\MobileUserController@store')->name('admin.mobileUsers.store')->middleware(['can:create,App\MobileUser']);

    Route::get('users/{user}', 'Admin\MobileUserController@show')->name('admin.users.show')->middleware(['can:view,user']);
    Route::patch('mobileUsers/{user}', 'Admin\MobileUserController@update')->name('admin.users.update')->middleware(['can:update,user']);
    Route::delete('mobileUsers/{mobile_user}', 'Admin\MobileUserController@destroy')->name('admin.mobileUsers.destroy')->middleware(['can:delete,App\MobileUser']);

    Route::get('admins', 'Admin\AdminController@index')->name('admin.admins.index');
    Route::post('admins', 'Admin\AdminController@store')->name('admin.admins.store')->middleware(['can:create,App\Admin']);
    Route::get('admins/{admin}', 'Admin\AdminController@show')->name('admin.admins.show');
    Route::patch('admins/{admin}', 'Admin\AdminController@update')->name('admin.admins.update')->middleware(['can:update,admin']);
    Route::delete('admins/{admin}', 'Admin\AdminController@destroy')->name('admin.admins.destroy')->middleware(['can:delete,admin']);

    Route::get('tours', 'Admin\TourController@index')->name('admin.tours.index');
    Route::post('tours', 'Admin\TourController@store')->name('admin.tours.store')->middleware(['can:create,App\Tour']);
    Route::post('tours/export', 'Admin\TourController@export')->name('admin.tours.export')->middleware(['can:view,App\Tour']);
    Route::get('tours/{tour}', 'Admin\TourController@show')->name('admin.tours.show')->middleware(['can:view,tour']);
    Route::patch('tours/{tour}/transfer', 'Admin\TourController@transfer')->name('admin.tours.transfer')->middleware(['can:update,tour']);
    Route::patch('tours/{tour}', 'Admin\TourController@update')->name('admin.tours.update')->middleware(['can:update,tour']);
    Route::delete('tours/{tour}', 'Admin\TourController@destroy')->name('admin.tours.destroy')->middleware(['can:delete,tour']);
    Route::put('tours/{tour}/media', 'Admin\TourController@uploadMedia')->name('admin.tours.media')->middleware(['can:view,tour']);

    Route::resource('tours/{tour}/stops', 'StopController', ['as' => 'admin'])->middleware(['can:update,tour']);
    Route::put('tours/{tour}/stops/{stop}/order', 'StopController@changeOrder')->name('admin.stops.order')->middleware(['can:update,tour']);
    Route::put('tours/{tour}/stops/{stop}', 'StopController@uploadMedia')->name('admin.stops.media')->middleware(['can:update,tour']);

    Route::patch('change-role/{user}', 'Admin\ChangeRoleController@update')->name('admin.change-role')->middleware(['can:update,user']);
    Route::patch('change-password/{user}', 'Admin\ChangePasswordController@update')->name('admin.change-password')->middleware(['can:update,user']);
    Route::patch('change-plan/{user}', 'Admin\ChangePlanController@update')->name('admin.change-plan')->middleware(['can:update,user']);
    Route::patch('deactivate/{user}', 'Admin\ActivateAccountController@deactivate')->name('admin.deactivate-user')->middleware(['can:update,user']);
    Route::patch('reactivate/{user}', 'Admin\ActivateAccountController@reactivate')->name('admin.reactivate-user')->middleware(['can:update,user']);
    Route::patch('reset/{user}', 'Admin\ResetScoresController@reset')->name('admin.reset-user')->middleware(['can:update,user']);
});
