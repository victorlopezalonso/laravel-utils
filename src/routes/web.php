<?php

use Illuminate\Support\Facades\Crypt;
use Victorlopezalonso\LaravelUtils\Console\Command;

/**
 * Executes the deploy command using api encrypted key
 */
Route::post('/deploy', function () {
    try {
        Crypt::decrypt(request()->get('key')) === env('APP_KEY') && Command::deploy();
    } catch (Illuminate\Contracts\Encryption\DecryptException $e) {
        abort(404);
    }
});
