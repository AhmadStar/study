<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Street\Http\Controllers\StreetController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'streets', 'as' => 'street.'], function () {
        Route::resource('', StreetController::class)->parameters(['' => 'street']);
    });
});
