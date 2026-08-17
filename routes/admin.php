<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LotteryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/', [LoginController::class, 'showForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
        });
        Route::middleware('auth:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
            Route::post('lotteries/{lottery}/draw', [LotteryController::class, 'draw'])
                ->name('lotteries.draw');

            Route::get('lotteries/{lottery}/draws/{draw}', [LotteryController::class, 'drawShow'])
                ->name('lotteries.draws.show');

            Route::get('lotteries/{lottery}', [LotteryController::class, 'show'])
                ->name('lotteries.show');

            Route::resource('lotteries', LotteryController::class)->except(['show']);
        });
        Route::resource('bank-accounts', BankAccountController::class);
        Route::controller(DepositController::class)->group(function () {
            Route::get('deposits', 'index')->name('deposits.index');
            Route::get('deposits/{deposit}', 'show')->name('deposits.show');
            Route::post('deposits/{deposit}/approve', 'approve')->name('deposits.approve');
            Route::post('deposits/{deposit}/reject', 'reject')->name('deposits.reject');
        });
        /*
        |--------------------------------------------------------------------------
        | Users Management
        |--------------------------------------------------------------------------
        */
        Route::controller(UserController::class)->group(function () {
            Route::get('/users', 'index')
                ->name('users.index');
        });
    });

    /*
|--------------------------------------------------------------------------
| Wallet Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')->prefix('admin/wallets')->name('admin.wallets.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Wallets
    |--------------------------------------------------------------------------
    */

    Route::get('/', [WalletController::class, 'index'])
        ->name('index');

    Route::get('/{wallet}', [WalletController::class, 'show'])
        ->name('show');

    /*
    |--------------------------------------------------------------------------
    | Wallet Transactions
    |--------------------------------------------------------------------------
    */

    Route::get('/{wallet}/transactions', [WalletController::class, 'transactions'])
        ->name('transactions');

    /*
    |--------------------------------------------------------------------------
    | Wallet Bonuses
    |--------------------------------------------------------------------------
    */

    Route::get('/{wallet}/bonuses', [WalletController::class, 'bonuses'])
        ->name('bonuses');

    /*
    |--------------------------------------------------------------------------
    | Wallet Reconciliation
    |--------------------------------------------------------------------------
    */

    Route::get('/{wallet}/reconciliation', [WalletController::class, 'reconciliation'])
        ->name('reconciliation');
});

/*
|--------------------------------------------------------------------------
| Withdrawal Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')->prefix('admin/withdrawals')->name('admin.withdrawals.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Requests
    |--------------------------------------------------------------------------
    */

    Route::get('/', [WithdrawalController::class, 'index'])
        ->name('index');

    Route::get('/{withdrawal}', [WithdrawalController::class, 'show'])
        ->name('show');

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    Route::post('/{withdrawal}/approve', [WithdrawalController::class, 'approve'])
        ->name('approve');

    Route::post('/{withdrawal}/reject', [WithdrawalController::class, 'reject'])
        ->name('reject');
});
    /*
    |--------------------------------------------------------------------------
    | KYC Managment
    |--------------------------------------------------------------------------
    */
  Route::middleware('auth:admin')
    ->prefix('admin/kyc')
    ->name('admin.kyc.')
    ->group(function () {

        Route::get('/', [KycController::class, 'index'])->name('index');
        Route::get('/{kyc}', [KycController::class, 'show'])->name('show');
        Route::post('/{kyc}/approve', [KycController::class, 'approve'])
        ->name('approve');
        Route::post('/{kyc}/reject', [KycController::class, 'reject'])
        ->name('reject');

    });