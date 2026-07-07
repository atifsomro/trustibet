<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/user-account', function () {
    return view('pages.user-account');
})->name('user-account');

Route::get('/notifications', function () {
    return view('pages.notifications');
})->name('notifications');

Route::view('/login', 'pages.login')->name('login');
Route::view('/register', 'pages.register')->name('register');
Route::view('/about', 'pages.about')->name('about');
Route::view('/welcome', 'pages.welcome')->name('welcome');
Route::view('/participate', 'pages.participate')->name('participate');
Route::view('/withdraw', 'pages.withdraw')->name('withdraw');
Route::view('/deposit', 'pages.deposit')->name('deposit');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot.password');

Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('reset.password');


Route::get('/winner-history', function () {
    return view('pages.winner.winner-history');
})->name('winner.history');

Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy');
})->name('privacy.policy');

Route::get('/terms-conditions', function () {
    return view('pages.terms-conditions');
})->name('terms.conditions');

Route::get('/404', function () {
    return view('errors.404');
})->name('404');

use App\Http\Controllers\GameController;
Route::get('/game/{slug}', [GameController::class, 'show'])->name('game.show');

use App\Http\Controllers\ScratchCardController;
Route::post('/scratch/reveal', [ScratchCardController::class, 'reveal'])
    ->name('scratch.reveal');