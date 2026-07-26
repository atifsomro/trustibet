<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        Session::flush();
        return redirect()->route('admin.login');
    }
}