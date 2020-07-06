<?php

use Illuminate\Support\Facades\Route;
use SergeevPasha\DPD\Http\Controllers\DPDController;
use SergeevPasha\DPD\Http\Controllers\EnumController;
use SergeevPasha\DPD\Http\Controllers\AuthDPDController;

Route::post('/authorize', [AuthDPDController::class, '__invoke'])
    ->name('dpd.auth');
Route::get('/cities', [DPDController::class, 'queryCity'])
    ->name('dpd.cities');
Route::get('/cities/{city}/streets', [DPDController::class, 'queryStreet'])
    ->name('dpd.cities.streets');
Route::get('/receivePointCities', [DPDController::class, 'queryReceivePointCity'])
    ->name('dpd.receivePoints.cities');
Route::get('/receivePoints', [DPDController::class, 'getReceivePoints'])
    ->name('dpd.receivePoints');
Route::get('/terminals', [DPDController::class, 'getTerminals'])
    ->name('dpd.terminals');
Route::post('/calculate', [DPDController::class, 'calculateDeliveryPrice'])
    ->name('dpd.calculate');

Route::get('/countries', [EnumController::class, 'countries'])
    ->name('dpd.countries');
Route::get('/services', [EnumController::class, 'services'])
    ->name('dpd.services');
