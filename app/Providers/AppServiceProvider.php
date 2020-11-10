<?php

namespace App\Providers;

use App\Helpers\Id;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('id', Id::class . '@validateId');
        Validator::extend('id_array', Id::class . '@validateIdArray');
    }
}
