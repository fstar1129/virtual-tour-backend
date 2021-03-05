<?php
/*
|--------------------------------------------------------------------------
| CMS API Routes
|--------------------------------------------------------------------------
|
*/
Route::put('profile/charge', 'AccountController@charge')->middleware('auth.apikey');

Route::middleware(['jwt.auth', 'role:client|admin|superadmin', 'active'])->group(function () {
    Route::get('session', 'AuthController@userSession');
    Route::get('profile', 'AccountController@show');
    Route::patch('profile', 'AccountController@update');
    Route::patch('profile/password', 'AccountController@changePassword');
    Route::put('profile/subscribe', 'AccountController@subscribe');
    Route::patch('profile/disable-popup', 'AccountController@disablePopup');

    Route::get('tours', 'TourController@index')->name('cms.tours.index');
    Route::post('tours', 'TourController@store')->name('cms.tours.store')->middleware(['can:create,App\Tour']);
    Route::get('tours/{tour}', 'TourController@show')->name('cms.tours.show')->middleware(['can:view,tour']);
    Route::put('tours/{tour}/stop-order', 'TourController@stopOrder')->name('cms.tour.order')->middleware(['can:update,tour']);
    Route::put('tours/{tour}/publish', 'TourController@publish')->name('cms.tours.publish')->middleware(['can:update,tour']);
    Route::put('tours/{tour}/unpublish', 'TourController@unpublish')->name('cms.tours.unpublish')->middleware(['can:update,tour']);
    Route::put('tours/{tour}/unpublish-by-rejection', 'TourController@unpublishByRejection')->name('cms.tours.unpublishWithRejection')->middleware(['can:update,tour']);
    Route::patch('tours/{tour}', 'TourController@update')->name('cms.tours.update')->middleware(['can:update,tour']);
    Route::post('tours/{tour}/promo-code', 'TourController@addPromoCode')->name('cms.tours.addPromoCode')->middleware(['can:update,tour']);
    Route::delete('tours/{tour}', 'TourController@destroy')->name('cms.tours.destroy')->middleware(['can:delete,tour']);
    Route::get('analytics/{tour}/overview', 'AnalyticsController@overview')->name('cms.analytics.overview')->middleware(['can:view,tour']);
    Route::get('analytics/{tour}/details', 'AnalyticsController@details')->name('cms.analytics.details')->middleware(['can:view,tour']);
    Route::get('analytics/{tour}/devices', 'AnalyticsController@devices')->name('cms.analytics.devices')->middleware(['can:view,tour']);

    Route::resource('tours/{tour}/stops', 'StopController', ['as' => 'cms'])->middleware(['can:update,tour']);
    Route::put('tours/{tour}/stops/{stop}/order', 'StopController@changeOrder')->name('cms.stops.order')->middleware(['can:update,tour']);

    Route::post('media/upload', 'MediaController@store')->middleware(['can:create,App\Media'])->name('cms.media');
});
