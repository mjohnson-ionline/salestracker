<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::crud('user', 'UserCrudController');
    Route::crud('clients', 'ClientsCrudController');
    Route::crud('proposals', 'ProposalsCrudController');
    Route::crud('reseller', 'ResellerCrudController');
    Route::crud('products', 'ProductsCrudController');
    Route::crud('deal', 'DealCrudController');
    Route::crud('invoice', 'InvoiceCrudController');
    Route::crud('payment', 'PaymentCrudController');
    Route::crud('line-item', 'LineItemCrudController');

    // route to show the comission report
    Route::get('comission/{start_date?}/{end_date?}/', 'ComissionController@index')->name('comission.index');
    Route::post('comission/report-single', 'ComissionController@reportSingle')->name('comission.single-report');
    Route::post('comission/send-single', 'ComissionController@sendSingle')->name('comission.single-report');
    Route::post('comission/report-all', 'ComissionController@reportAll')->name('comission.single-all');
}); // this should be the absolute last line of this file
