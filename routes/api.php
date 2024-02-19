<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Public alive test endpoint
Route::get(
    '/',
    fn() => response(
        "Tenet invoice details demo. Mounted over Laravel " . app()->version(),
        200));

Route::resource('customers', \App\Http\Controllers\CustomerController::class)->only([
    'index', 'show', 'destroy'
]);
Route::prefix('customers')->group( function() {
    Route::prefix('/{customer}')->group(function(){
        Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'customerList']);
        Route::post('/create-invoice', [\App\Http\Controllers\InvoiceController::class, 'createInvoiceForCustomer']);
    });
});

Route::resource('invoices', \App\Http\Controllers\InvoiceController::class)->only([
    'index', 'show', 'destroy'
]);

Route::resource('services', \App\Http\Controllers\ServiceController::class)->only([
    'index', 'show'
]);

Route::resource('services/{service}/consumptions', \App\Http\Controllers\ServiceConsumptionController::class)->only([
    'index', 'store'
]);

Route::resource('service-consumptions', \App\Http\Controllers\ServiceConsumptionController::class)->only([
    'show', 'destroy'
]);
