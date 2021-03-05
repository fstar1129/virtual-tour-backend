<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::post('auth/login/facebook', 'AuthController@facebook')->name('facebook.login');
Route::post('auth/login/google', 'AuthController@google')->name('google.login');
Route::post('auth/login', 'AuthController@login');
Route::post('auth/signup', 'AuthController@signup');
Route::post('auth/forgot-password', 'ResetPasswordController@forgot');
Route::post('auth/reset-password', 'ResetPasswordController@reset');
Route::post('confirm-email', 'ConfirmEmailController@confirm')->name('confirm-email');
// Route::get('test', 'Controller@test');

Route::post('purchase-junket-from-peek', 'TourController@purchaseJunketFromPeek')->name('purchase-junket-from-peek')->middleware('auth.apikey');
Route::post('purchase-junket-from-fareharbor-test/{tour}', 'TourController@purchaseJunketFromFareHarborTest')->name('purchase-junket-from-fareharbor-test');
Route::post('purchase-junket-from-fareharbor/{tour}', 'TourController@purchaseJunketFromFareHarbor')->name('purchase-junket-from-fareharbor');
Route::post('purchase-junket-from-thirdparty', 'TourController@purchaseJunketFromThirdparty')->name('purchase-junket-from-thirdparty')->middleware('auth.apikey');

Route::middleware(['jwt.auth', 'active'])->group(function () {
    Route::get('auth/session', 'AuthController@userSession');
    Route::delete('auth/facebook', 'AuthController@facebookDetach')->name('facebook.detach');
    Route::post('auth/facebook/attach', 'AuthController@facebookAttach')->name('facebook.attach');
});

Route::middleware(['jwt.refresh'])->group(function () {
    Route::get('auth/refresh', function () {
        return response(null, 204);
    });
});
