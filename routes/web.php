<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\SignController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/webhook/pipedrive', [\App\Http\Controllers\WebHooksController::class, 'pipedrive'])->name('webhooks.pipedrive')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhook/xero', [\App\Http\Controllers\WebHooksController::class, 'xero'])->name('webhooks.xero')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/webhook/view/{id?}', [\App\Http\Controllers\WebHooksController::class, 'view'])->name('webhooks.view');
Route::get('/webhook/unserial', [\App\Http\Controllers\WebHooksController::class, 'unserial'])->name('webhooks.unserial');
Route::get('/webhook/unserial_manual', [\App\Http\Controllers\WebHooksController::class, 'unserial_manual'])->name('webhooks.unserial_manual');
Route::get('/webhook/unserial_decode', [\App\Http\Controllers\WebHooksController::class, 'unserial_decode'])->name('webhooks.unserial_decode');
Route::get('/webhook/view_transactions', [\App\Http\Controllers\WebHooksController::class, 'view_transactions'])->name('webhooks.view_transactions');
Route::get('/webhook/view_specific_transactions/{id?}', [\App\Http\Controllers\WebHooksController::class, 'view_specific_transactions'])->name('webhooks.view_specific_transactions');

Route::redirect('/', '/admin');

Route::get('/manage/xero', [\App\Http\Controllers\XeroController::class, 'index'])->name('xero.auth.success');

// Route::get('/sign/{id}/{token}', [SignController::class, 'index'])->name('sign');
// Route::post('/store/{id}', [SignController::class, 'store'])->name('store');
// Route::get('/thankyou/{id}/{token}', [SignController::class, 'thankyou'])->name('thankyou');
// Route::get('/test', [\App\Http\Controllers\XeroController::class, 'index'])->name('xero.auth.success');

// sync stuff from pipedrive and xero manually
// Route::get('/webhook/sync_xero', [\App\Http\Controllers\WebHooksController::class, 'sync_xero'])->name('webhooks.sync_xero');
// Route::get('/webhook/sync_pipedrive_products', [\App\Http\Controllers\WebHooksController::class, 'sync_pipedrive_products'])->name('webhooks.sync_pipedrive_products');
