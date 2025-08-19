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

Theme::registerRoutes(function (): void {
    Route::group([
        'namespace' => 'Botble\Family\Http\Controllers',
        'middleware' => ['web', 'core'],
    ], function (): void {
        Route::group([], function (): void {
            Route::get('registerFamily', 'FamilyController@showRegistrationFamilyForm')->name('public.registerFamily');
            Route::post('registerFamily', 'FamilyController@registerFamily')->name('public.registerFamily.post');
        });
    });
});
