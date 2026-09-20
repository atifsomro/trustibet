<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\KycController;
use Illuminate\Support\Facades\Route;
use App\Models\Kyc;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/user-account', function () {
    $kyc = Kyc::query()->where('user_id', auth('web')->user()->id)->first();
    return view('pages.user-account', compact('kyc'));
})->middleware('auth')->name('user-account');


Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])
    ->middleware('auth')
    ->name('notifications');

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

Route::get('/404', function () {
    return view('errors.404');
})->name('404');

// CMS pages managed from Admin > Pages. Served at /page/{slug} so the
// existing hardcoded routes above (/about, /privacy-policy, etc.) are
// left completely untouched.
Route::get('/page/{slug}', [PageController::class, 'show'])
    ->name('page.show');

use App\Http\Controllers\GameController;
use App\Http\Controllers\LotteryController;
use App\Http\Controllers\User\InvestmentController;

Route::get('/game/{slug}', [GameController::class, 'show'])->middleware('auth')->name('game.show');

use App\Http\Controllers\ScratchCardController;
use App\Http\Controllers\User\ProfileController;

Route::post('/scratch/reveal', [ScratchCardController::class, 'reveal'])
    ->name('scratch.reveal');

Route::middleware('auth')->group(function () {
    Route::get('/investment', [InvestmentController::class, 'index'])->name('investment');
    Route::post('/investments/{package}/buy', [InvestmentController::class, 'buy'])->name('investments.buy');
    Route::get('/my-investments', [InvestmentController::class, 'mine'])->name('investments.mine');
    Route::get('/my-investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
    Route::post('/investments/claim', [InvestmentController::class, 'claim'])->name('investments.claim');
    Route::post('/investments/transfer-roi', [InvestmentController::class, 'transfer'])->name('investments.transfer');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.avatar.update');
});

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

    // Google Social Auth
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback')->name('auth.google.callback');
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
    | KYC Verification
    |--------------------------------------------------------------------------
    */
    Route::get('/kyc', [KycController::class, 'index'])
        ->name('kyc.index');

    Route::post('/kyc', [KycController::class, 'store'])
        ->name('kyc.store');

    /*
    |--------------------------------------------------------------------------
    | Wallet API
    |--------------------------------------------------------------------------
    */
    Route::get('/balance', [WalletController::class, 'balance'])
        ->name('balance');


});

Route::middleware('auth')->group(function () {

    Route::get('/lotteries', [LotteryController::class, 'index'])
        ->name('lotteries.index');

    Route::get('/my-lottery-history', [LotteryController::class, 'history'])
        ->name('lotteries.history');

    Route::post('/lotteries/draw-due', [LotteryController::class, 'drawDueAll'])
        ->middleware('throttle:20,1')
        ->name('lotteries.draw-due.all');

    Route::post('/lotteries/{lottery}/draw-due', [LotteryController::class, 'drawDue'])
        ->middleware('throttle:20,1')
        ->name('lotteries.draw-due');

    Route::get('/lotteries/{lottery}/live-html', [LotteryController::class, 'liveHtml'])
        ->name('lotteries.live-html');

    Route::get('/lotteries/{lottery}', [LotteryController::class, 'show'])
        ->name('lotteries.show');

    Route::get('/lotteries/{lottery}/results', [LotteryController::class, 'results'])
        ->name('lotteries.results');

    Route::post('/lotteries/{lottery}/tickets', [LotteryController::class, 'buyTickets'])
        ->name('lotteries.tickets.buy');
    Route::get('/lotteries/{lottery}/draws/{draw}', [LotteryController::class, 'drawShow',])->name('lotteries.draws.show');

    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])
        ->name('notifications.read');
});
