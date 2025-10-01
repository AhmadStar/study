<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Family\Http\Controllers\FamilyController;
use Illuminate\Support\Facades\Route;
use Botble\Theme\Facades\Theme;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'families', 'as' => 'family.'], function () {
        Route::resource('', FamilyController::class)->parameters(['' => 'family']);
    });
});

Route::group(['namespace' => 'Botble\Family\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::get('family-filter/list', [
            'as' => 'family-filter-list',
            'uses' => 'FamilyController@familyFilterList',
            'permission' => 'family.index',
        ]);

        Route::get('family-list/show', [
            'as' => 'family-show-list',
            'uses' => 'FamilyController@familyList',
            'permission' => 'family.index',
        ]);


         });

});


Theme::registerRoutes(function (): void {
    Route::group([
        'namespace' => 'Botble\Family\Http\Controllers',
        'middleware' => ['web', 'core'],
    ], function (): void {

        Route::group([], function (): void {
            Route::post('/families', [\Botble\Family\Http\Controllers\FamilyController::class, 'storeF'])
                ->name('family.store');

            Route::get('registerFamily', 'FamilyController@showRegistrationFamilyForm')->name('public.registerFamily');
            Route::post('registerFamily', 'FamilyController@registerFamily')->name('public.registerFamily.post');
        });

        Route::get('ajax/buildings-by-area', [FamilyController::class, 'getBuildingsByArea'])
            ->name('ajax.buildings.by-area');
    });
});
