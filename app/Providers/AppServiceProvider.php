<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Resources\TourResource;
use Illuminate\Support\Facades\Schema;
// use DB;
// use File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TourResource::withoutWrapping();
        Schema::defaultStringLength(191);
        // query log. I stopped it to increase the server speed

        // DB::listen(function($query) {
        //     if (!str_contains($query->sql, 'select')) {
        //         File::append(
        //             storage_path('/logs/query.log'),
        //             '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
        //         );
        //     }
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
