<?php

use Botble\Base\Facades\AdminHelper;
use Botble\District\Http\Controllers\DistrictController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'districts', 'as' => 'district.'], function () {
        Route::resource('', DistrictController::class)->parameters(['' => 'district']);
    });
});
