<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\minipaycontroller;

Route::group(['domain' => 'minipay.'.config('app.my_domain')], function() {
    Route::controller(minipaycontroller::class)->group(function() {
        Route::get('/', 'index')->name('minipay.home');
        Route::prefix('profile')->group(function() {
            Route::get('/view/{address?}', 'profile')->name('minipay.profile');
            Route::get('/delete-favorite/{id}', 'delete_favorite')->name('minipay.delete.favorite');
            Route::post('/update/{id}', 'update_profile')->name('minipay.update.profile');
        });
        Route::prefix('mpesa')->group(function() {
            Route::get('/paybill/{address?}', 'mpesa_paybill')->name('minipay.mpesa.paybill');
            Route::get('/till/{address?}', 'mpesa_till')->name('minipay.mpesa.till');
            Route::get('/send-money/{address?}', 'mpesa_send_money')->name('minipay.mpesa.send.money');
            Route::get('/pay', 'mpesa_pay')->name('minipay.mpesa.pay');
            Route::get('/review/{hash}', 'mpesa_pay_review')->name('minipay.mpesa.pay.review');
            Route::post('/confirm', 'mpesa_confirm')->name('minipay.mpesa.confirm');
            Route::get('/all/{address}', 'mpesa_transactions')->name('minipay.mpesa.transactions');
            Route::get('/get/rates/{country}', 'get_rates')->name('minipay.get.rates');
            Route::post('/refund', 'refund')->name('minipay.refund');
            Route::post('/log/error', 'log_error')->name('minipay.log.error');

            Route::get('/recent-transactions/{address}', 'recent_transactions')->name('minipay.recent.transactions');
        });
        Route::prefix('airtime')->group(function() {
            Route::get('/buy/{currency_code?}', 'buy_airtime')->name('minipay.airtime.buy');
            Route::get('/pay', 'airtime_pay')->name('minipay.airtime.pay');
        });
    });
});
