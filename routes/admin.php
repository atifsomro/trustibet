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
use App\Http\Controllers\Admin\InvestmentPackageController;
use App\Http\Controllers\Admin\UserInvestmentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GamePlayController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/', [LoginController::class, 'showForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
        });
        Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/logout', [LoginController::class, 'logout'])
            ->name('logout');
        /*
        |--------------------------------------------------------------------------
        | Lottery Management
        |--------------------------------------------------------------------------
        */
        // Resource routes
        Route::resource('lotteries', LotteryController::class)
            ->except(['show']);
        // Lottery details
        Route::get('lotteries/{lottery}', [LotteryController::class, 'show'])
            ->withTrashed()
            ->name('lotteries.show');
        // Purchase & draw history
        Route::get('lottery-history', [LotteryController::class, 'history'])
            ->name('lotteries.history');
        // Draw lottery (kept as a fallback; primary path is the scheduler)
        Route::post('lotteries/{lottery}/draw', [LotteryController::class, 'draw'])
            ->name('lotteries.draw');
        // Individual draw results
        Route::get(
                'lotteries/{lottery}/draws/{draw}',
                [LotteryController::class, 'drawShow']
            )->withTrashed()->name('lotteries.draws.show');

        /*
        |--------------------------------------------------------------------------
        | Investment Management
        |--------------------------------------------------------------------------
        */
        Route::resource('investment-packages', InvestmentPackageController::class)
            ->except(['show']);
        Route::get('user-investments', [UserInvestmentController::class, 'index'])
            ->name('user-investments.index');
        Route::get('user-investments/{userInvestment}', [UserInvestmentController::class, 'show'])
            ->name('user-investments.show');
        Route::put('user-investments/{userInvestment}/daily-roi', [UserInvestmentController::class, 'updateDailyRoi'])
            ->name('user-investments.update-daily-roi');

        /*
        |--------------------------------------------------------------------------
        | Games Management
        |--------------------------------------------------------------------------
        */
        Route::resource('games', GameController::class)->except(['show']);
        Route::post('games/{game}/packages', [GameController::class, 'storePackage'])
            ->name('games.packages.store');
        Route::put('games/{game}/packages/{package}', [GameController::class, 'updatePackage'])
            ->name('games.packages.update');
        Route::delete('games/{game}/packages/{package}', [GameController::class, 'destroyPackage'])
            ->name('games.packages.destroy');
        Route::post('games/{game}/packages/{package}/prizes', [GameController::class, 'storePrize'])
            ->name('games.packages.prizes.store');
        Route::put('games/{game}/packages/{package}/prizes/{prize}', [GameController::class, 'updatePrize'])
            ->name('games.packages.prizes.update');
        Route::delete('games/{game}/packages/{package}/prizes/{prize}', [GameController::class, 'destroyPrize'])
            ->name('games.packages.prizes.destroy');
        Route::get('game-plays', [GamePlayController::class, 'index'])
            ->name('game-plays.index');
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

/*
|--------------------------------------------------------------------------
| Settings (dynamic frontend config)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')
    ->prefix('admin/settings')
    ->name('admin.settings.')
    ->controller(SettingController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'update')->name('update');
        Route::delete('/{setting}', 'destroy')->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| CMS Pages (About Us, Privacy Policy, etc.)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('pages', PageController::class)->except(['show']);
    });