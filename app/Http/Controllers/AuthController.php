<?php

namespace App\Http\Controllers;

use App\Actions\Auth\RegisterUserAction;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Admin\Page;
use App\Models\User;
use App\Mail\ForgotPassword;
use App\Http\Controllers\Controller;
use App\Mail\UserVerificationMail;
use App\Models\Country;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        protected RegisterUserAction $registerUserAction
    ) {}
    public function showLoginForm()
    {
        $title = 'Login - ' . env('APP_NAME');
        return view('auth.login', compact('title'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            if (is_null($user->email_verified_at)) {
                Mail::to($user->email)->send(new UserVerificationMail($user));
                Auth::logout();
                return redirect()
                    ->route('auth.showVerificationForm')
                    ->with('email', $user->email)
                    ->with('error', 'Please verify your email address before logging in.');
            }
            return redirect()->route('home');
        }
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Credentials do not match.');
    }

    public function showRegisterForm()
    {
        $title = 'Register - ' . env('APP_NAME');
        $countries = Country::all();
        return view('auth.register', compact('title', 'countries'));
    }

    public function register(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required|string|min:3|max:30',
            'username' => 'required|min:3|max:20|alpha_dash|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'country_id' => 'required',
            'password' => 'required|min:8',
        ]);
        // $inserted = (new User())->store($request);
        $inserted = $this->registerUserAction->execute($request);
        if ($inserted) {
            return redirect()
                ->route('auth.showVerificationForm')
                ->with('success', 'You have successfully created an account.')
                ->with('email', $request->email)
                ->with('info', 'We have sent a verification email to your registered email address.');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating your account. Please try again.');
        }
    }
    public function showResendForm(Request $request)
    {
        $title = 'Resend Verification Code - ' . env('APP_NAME');
        return view('auth.resend-verification', compact('title'));
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::query()
            ->where('email', $request->email)
            ->first();
        if (! $user) {
            return redirect()
                ->back()
                ->with('error', 'User not found!');
        }
        // Send the verification email again
        Mail::to($user->email)->send(new UserVerificationMail($user));
        return redirect()
            ->route('auth.showVerificationForm')
            ->with('email', $user->email)
            ->with('success', 'Verification email sent successfully.')
            ->with('info', 'Please check your inbox and spam folder for the verification email.');
    }

    public function showVerificationForm()
    {
        $title = 'Email Verification - ' . env('APP_NAME');
        return view('auth.verification', compact('title'));
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);
        $user = User::firstWhere('verification_code', $request->code);
        if (! $user) {
            return back()->with('error', 'Invalid verification code.');
        }
        $user->verification_code = null;
        $user->email_verified_at = now();
        $user->save();
        return redirect()
            ->route('auth.login')
            ->with('success', 'Your email has been verified successfully. You can now log in.');
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        Session::flush();
        return redirect()->route('auth.login');
    }

    public function forgotPasswordForm()
    {
        $title = 'Forgot Password - ' . env('APP_NAME');
        return view('auth.forgot-password', compact('title'));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::query()->where('email', $request->email)->first();
        // Don't reveal whether email exists
        if (!$user || !empty($user->password_reset_token)) {
            return back()->with('success', 'A password reset link has been sent.');
        }
        $token = Str::random(64);
        $user->password_reset_token = hash('sha256', $token);
        $user->save();
        $link = URL::temporarySignedRoute('auth.password.reset', now()->addHour(), ['user' => $user->id, 'token' => $token]);
        Mail::to($user->email)->send(new ResetPasswordMail($user, $link));
        return back()->with(
            'success',
            'A password reset link has been sent.'
        );
    }
    public function showResetPasswordForm(User $user)
    {
        return view('auth.reset-password', [
            'user' => $user,
            'token' => request()->token,
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $user = User::findOrFail($request->user_id);
        if (!hash_equals($user->password_reset_token, hash('sha256', request('token')))) {
            abort(403);
        }
        $user->password_reset_token = null;
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()
            ->route('auth.login')
            ->with('success', 'Password changed successfully.');
    }
}
