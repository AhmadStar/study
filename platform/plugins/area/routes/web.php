<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Area\Http\Controllers\AreaController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'areas', 'as' => 'area.'], function () {
        Route::resource('', AreaController::class)->parameters(['' => 'area']);
    });
});
