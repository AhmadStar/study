<?php

use Botble\Building\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Building\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('neighborhood-map', [FrontendController::class, 'map'])->name('neighborhood.map');
    Route::get('person/{id}', [FrontendController::class, 'personDetail'])->name('person.detail');
    Route::get('building/{id}/residents', [FrontendController::class, 'getResidents'])->name('building.residents');
});
