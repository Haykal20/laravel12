<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::redirect('/', '/mahasiswa');
    // Resource route untuk mahasiswa
    Route::resource('mahasiswa', MahasiswaController::class);
    
    // Atau jika ingin define manual:
    /*
    Route::get('/mahasiswa', 'MahasiswaController@index')->name('mahasiswa.index');
    Route::post('/mahasiswa', 'MahasiswaController@store')->name('mahasiswa.store');
    Route::get('/mahasiswa/{id}/edit', 'MahasiswaController@edit')->name('mahasiswa.edit');
    Route::put('/mahasiswa/{id}', 'MahasiswaController@update')->name('mahasiswa.update');
    Route::delete('/mahasiswa/{id}', 'MahasiswaController@destroy')->name('mahasiswa.destroy');
    */
});
