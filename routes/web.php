<?php
use Illuminate\Support\Facades\Route;
use Marcoaacoliveira\LaravelPagseguro\LaravelPagseguro;

Route::post('pagseguro_notification', [LaravelPagseguro::class, 'notification'])->name('laravel-pagseguro.notification');
Route::get('pagseguro_redirect', [LaravelPagseguro::class, 'redirect'])->name('laravel-pagseguro.redirect');
