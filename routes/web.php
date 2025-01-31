<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    Mail::raw('Correo de prueba', function ($message) {
        $message->to('jcabrerasa22dw@ikzubirimanteo.com')
                ->subject('Prueba de correo');
    });

    return 'Correo enviado';
});
