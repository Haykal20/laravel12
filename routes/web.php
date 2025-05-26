<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Auth;

Auth::routes(); // Tambahkan baris ini sebelum route mahasiswa

Route::get('/', function () {
    return redirect('/mahasiswa');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::put('/mahasiswa/{id}', 'MahasiswaController@update')->name('mahasiswa.update');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
