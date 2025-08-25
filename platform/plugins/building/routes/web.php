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
Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => ['web', 'auth']], function () {
    Route::post('buildings/from-map', [
        'as' => 'building.create.from.map',
        'uses' => 'Botble\Building\Http\Controllers\BuildingController@storeFromMap',
    ]);
// List areas as JSON for the modal dropdown
    Route::get('building/areas', [\Botble\Area\Http\Controllers\AreaController::class, 'getList'])
        ->name('building.areas.list');

    // Optionally: get areas for select
    Route::get('buildings/areas', [
        'as' => 'building.areas.index',
        'uses' => 'Botble\Building\Http\Controllers\AreaController@index',
    ]);
});


Route::group(['namespace' => 'Botble\Building\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('neighborhood-map', [FrontendController::class, 'map'])->name('neighborhood.map');
    Route::get('person/{id}', [FrontendController::class, 'personDetail'])->name('person.detail');
    Route::get('building/{id}/residents', [FrontendController::class, 'getResidents'])->name('building.residents');
    Route::get('building-info/{id}', [FrontendController::class, 'getBuildingInfo'])->name('building.info');
});

Route::group(['namespace' => 'Botble\Family\Http\Controllers', 'prefix' => 'admin/families', 'middleware' => ['web', 'core']], function () {
    // عرض صفحة التعديل
    Route::get('{id}/edit', [FrontendController::class, 'editFamily'])->name('families.edit');

    // حفظ التعديلات (update)
    Route::put('{id}', [FrontendController::class, 'updateFamily'])->name('families.update');

    // Delete family
    Route::delete('{id}/delete', [FrontendController::class, 'deleteFamily'])->name('families.delete');

    // Add family
    Route::get('{id}/add', [FrontendController::class, 'addFamily'])->name('families.add');

});
