<?php

use Botble\Base\Facades\AdminHelper;
use Botble\City\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'cities', 'as' => 'city.'], function () {
        Route::resource('', CityController::class)->parameters(['' => 'city']);
    });
});
