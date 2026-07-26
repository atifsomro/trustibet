<?php

namespace App\Http\Controllers;

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

class AuthController extends Controller
{
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
            'phone' => 'required|max:12',
            'country_id' => 'required',
            'password' => 'required|min:8',
        ]);
        $inserted = (new User())->store($request);
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
        Auth::guard('frontend')->logout();
        Session::flush();
        return redirect()->route('frontend.signIn');
    }

    public function handleUpdate(Request $request, $id)
    {
        $customer = Customer::where('id', $id)->first();
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'telephone' => 'required|regex:/^\d{3}?[-]?\d{3}[-]?\d{4}$/',
        ]);

        $update = (new Customer())->_update($request, $id);

        if ($update) {
            return redirect()->back()->with('success', 'Customer Account Information Updated Successfully.');
        }
    }

    public function forgotPassword()
    {
        $cmsData = (new Page('en'))->getCmsPage('forgot-password');

        $title = ($cmsData) ? $cmsData->title : 'Forgot Password';
        $meta_title = ($cmsData) ? $cmsData->meta_title : 'Forgot Password';
        $meta_description = ($cmsData) ? $cmsData->meta_description : "Forgot Password";
        $meta_keyword = ($cmsData) ? $cmsData->meta_keyword : "Forgot Password";
        $meta_image = asset('storage/config_logos/' . getWebsiteLogo());
        $meta_url = route('frontend.forgotPassword');

        return view('frontend.auth.forgot_password', compact('title', 'meta_title', 'meta_description', 'meta_keyword', 'meta_image', 'meta_url'));
    }

    public function handleForgotPassword(Request $request)
    {
        // return $request;
        $new_pass = random_password(10);
        Customer::where('email', $request->email)->update(['password' => Hash::make($new_pass)]);
        Mail::to($request->email)->send(new ForgotPassword($new_pass));

        return redirect()->back()->with('success', 'New password has been e-mailed to you.');
    }
}
