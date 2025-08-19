<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Person\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'people', 'as' => 'person.'], function () {
        Route::resource('', PersonController::class)->parameters(['' => 'person']);
    });
});
