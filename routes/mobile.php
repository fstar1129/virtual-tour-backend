<?php
/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
|
 */

Route::namespace('App\Http\Controllers')->group(function () {
    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/signup', 'AuthController@signup');
    Route::post('auth/login/facebook', 'AuthController@facebook');
    Route::post('auth/forgot-password', 'ResetPasswordController@forgot');

    Route::middleware(['jwt.refresh'])->group(function () {
        Route::get('auth/refresh', function () {
            return response(null, 204);
        });
    });

});

Route::namespace('App\Mobile\Controllers')->group(function () {
    Route::get('tours/sharable/{tour_id}', 'TourController@showTour')->middleware('auth.apikey');
    Route::get('tours/free', 'TourController@getFreeTours')->middleware('auth.apikey');
    Route::get('tours/free/{tour_id}', 'TourController@getFreeTour')->middleware('auth.apikey');

    Route::middleware(['jwt.auth', 'role:user|client|admin|superadmin', 'active'])->group(function () {
        Route::get('tours/all', 'TourController@all')->name('mobile.tours.all');
        Route::get('tours', 'TourController@index')->name('mobile.tours.index');
        Route::get('tours/mine', 'JoinedToursController@index');

        Route::post('tours/{tour}/track', 'ActivityController@tour');
        Route::post('stops/{stop}/track', 'ActivityController@stop');
        Route::post('device', 'DeviceController@store')->name('mobile.device.store');
        Route::get('tours/{tour}', 'TourController@show')->name('mobile.tours.show');
        Route::post('tours/{tour}/purchase', 'TourController@purchase')->name('mobile.tours.purchase');
        Route::get('tours/{tour}/promo_code/{promo_code}', 'TourController@checkPromoCode')->name('mobile.tours.check-promo-code');

        Route::post('profile', 'ProfileController@update')->name('mobile.profile.update');
        Route::patch('profile/password', 'ProfileController@password')->name('mobile.profile.password');
        Route::get('profile/{user}', 'ProfileController@show')->name('mobile.profile.show');
        Route::post('profile/change-email', 'ChangeEmailController@request')->name('mobile.profile.change-email');
        Route::post('profile/change-email/confirm', 'ChangeEmailController@confirm')->name('mobile.profile.change-email.confirm');
        Route::post('profile/avatar', 'AvatarController@store')->name('mobile.profile.avatar');
        Route::post('profile/tokens/purchase', 'ProfileController@purchase')->name('mobile.profile.purchase-token');
        Route::get('profile/unclaimed_prizes/{user}', 'ProfileController@unclaimedPrizes')->name('mobile.profile.unclaimed-prizes');

        Route::post('scores/start', 'ScoreCardController@start')->name('mobile.scores.start');
        Route::post('scores/{scoreCard}/progress', 'ScoreCardController@progress')->name('mobile.scores.progress');
        Route::get('scores/{user}', 'ScoreCardController@index')->name('mobile.scores.index');
        Route::get('scores/find/{tour}', 'ScoreCardController@find')->name('mobile.scores.find');

        Route::get('leaderboard/{tour}', 'LeaderboardController@tour')->name('mobile.leaderboard.tour');
        Route::get('leaderboard', 'LeaderboardController@index')->name('mobile.leaderboard');

        Route::get('reviews/{tour}', 'ReviewController@index')->name('mobile.reviews');
        Route::post('reviews/{tour}', 'ReviewController@store')->name('mobile.reviews.store');
        Route::delete('reviews/{tour}', 'ReviewController@destroy')->name('mobile.reviews.destroy');

        Route::get('favorites/{user}', 'FavoriteController@index')->name('mobile.favorites');
        Route::post('favorites/{tour}', 'FavoriteController@store')->name('mobile.favorites.store');
        Route::delete('favorites/{tour}', 'FavoriteController@destroy')->name('mobile.favorites.destroy');
    });

    // No-Authorization Endpoints
    Route::get('tours/anon/{tour}', 'TourController@show')->name('mobile.tours.show');
});


Route::namespace('App\Http\Controllers')->middleware(['jwt.auth', 'role:user|client|admin|superadmin'])->group(function () {
    Route::get('auth/session', 'AuthController@userSession');
});
