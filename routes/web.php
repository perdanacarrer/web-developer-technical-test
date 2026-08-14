<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/movies');

// Language switcher — available whether logged in or not.
Route::get('/lang/{locale}', 'LocaleController@switch')->name('lang.switch');

// Guest-only: login
Route::middleware('guest')->group(function () {
    Route::get('/login', 'AuthController@showLogin')->name('login');
    Route::post('/login', 'AuthController@login')->name('login.attempt');
});

Route::post('/logout', 'AuthController@logout')->name('logout');

// Everything below requires the hardcoded login.
Route::middleware('auth')->group(function () {
    Route::get('/movies', 'MovieController@index')->name('movies.index');
    Route::get('/movies/search', 'MovieController@search')->name('movies.search');
    Route::get('/movies/{imdbId}', 'MovieController@show')->name('movies.show');

    Route::get('/favorites', 'FavoriteController@index')->name('favorites.index');
    Route::post('/favorites', 'FavoriteController@store')->name('favorites.store');
    Route::delete('/favorites/{imdbId}', 'FavoriteController@destroy')->name('favorites.destroy');
});
