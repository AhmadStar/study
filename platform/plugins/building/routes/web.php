<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Building\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;
use Botble\Building\Http\Controllers\FrontendController;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'buildings', 'as' => 'building.'], function () {
        Route::resource('', BuildingController::class)->parameters(['' => 'building']);
    });
});


Route::group(['namespace' => 'Botble\Building\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('neighborhood-map', [FrontendController::class, 'map'])->name('neighborhood.map');
    Route::get('person/{id}', [FrontendController::class, 'personDetail'])->name('person.detail');
    Route::get('building/{id}/residents', [FrontendController::class, 'getResidents'])->name('building.residents');
});
