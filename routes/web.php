<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\User\WalletController;
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

// Route::view('/login', 'pages.login')->name('login');
// Route::view('/register', 'pages.register')->name('register');
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

Route::get('/lottery', function () {
    return view('pages.lottery');
})->name('lottery');

Route::get('/investment', function () {
    return view('pages.investment');
})->name('investment');

Route::get('/404', function () {
    return view('errors.404');
})->name('404');


use App\Http\Controllers\GameController;
Route::get('/game/{slug}', [GameController::class, 'show'])->middleware('auth')->name('game.show');

use App\Http\Controllers\ScratchCardController;
Route::post('/scratch/reveal', [ScratchCardController::class, 'reveal'])
    ->name('scratch.reveal');

### FRONTEND AUTH ROUTE ###
Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('auth.login');
    Route::post('login', 'login')->name('auth.login.post');
    Route::get('register', 'showRegisterForm')->name('auth.showRegisterForm');
    Route::post('register', 'register')->name('auth.register');
    Route::get('email-verification', 'showVerificationForm')->name('auth.showVerificationForm');
    Route::post('email-verification', 'emailVerification')->name('auth.emailVerification');
    Route::get('resend-code', 'showResendForm')->name('auth.showResendForm');
    Route::post('resend-code', 'resendCode')->name('auth.resendCode');
    Route::get('forgot-password', 'forgotPasswordForm')->name('auth.forgotPasswordForm');
    Route::post('forgot-password', 'forgotPassword')->name('auth.forgotPassword');
    Route::get('reset-password/{user}', 'showResetPasswordForm')
        ->middleware('signed')
        ->name('auth.password.reset');
    Route::post('reset-password', 'resetPassword')
        ->name('auth.password.update');
    Route::get('logout', 'logout')->name('auth.logout');
});

Route::middleware('auth')->prefix('wallet/deposits')->name('deposits.')->controller(DepositController::class)->group(function () {
    // List all user deposits
    Route::get('/', 'index')->name('index');
    // Create deposit
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
});

/*
|--------------------------------------------------------------------------
| User Wallet Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('wallet')->name('wallet.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', [WalletController::class, 'index'])
        ->name('index');

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */
    Route::get('/transactions', [WalletController::class, 'transactions'])
        ->name('transactions');
    Route::get('/transactions/{transaction}', [WalletController::class, 'showTransaction'])
    ->name('transactions.show');
    /*
    |--------------------------------------------------------------------------
    | Bonuses
    |--------------------------------------------------------------------------
    */
    Route::get('/bonuses', [WalletController::class, 'bonuses'])
        ->name('bonuses');
    /*
    |--------------------------------------------------------------------------
    | Withdrawals
    |--------------------------------------------------------------------------
    */
    Route::get('/withdrawals', [WalletController::class, 'withdrawals'])
        ->name('withdrawals');
    Route::get('/withdrawals/create', [WalletController::class, 'createWithdrawal'])
        ->name('withdrawals.create');
    Route::post('/withdrawals', [WalletController::class, 'storeWithdrawal'])
        ->name('withdrawals.store');
    /*
    |--------------------------------------------------------------------------
    | Wallet API
    |--------------------------------------------------------------------------
    */
    Route::get('/balance', [WalletController::class, 'balance'])
        ->name('balance');
});
