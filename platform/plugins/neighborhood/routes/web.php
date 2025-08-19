<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Neighborhood\Http\Controllers\NeighborhoodController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'neighborhoods', 'as' => 'neighborhood.'], function () {
        Route::resource('', NeighborhoodController::class)->parameters(['' => 'neighborhood']);
    });
});
