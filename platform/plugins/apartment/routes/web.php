<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Apartment\Http\Controllers\ApartmentController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'apartments', 'as' => 'apartment.'], function () {
        Route::resource('', ApartmentController::class)->parameters(['' => 'apartment']);
    });
});
