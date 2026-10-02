<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\Google2faService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected Google2faService $google2fa
    ) {}

    /**
     * Display all registered users.
     */
    public function index(Request $request): View
    {
        $query = User::query()->with('wallet');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function enableGoogle2fa(User $user): RedirectResponse
    {
        $this->google2fa->enableFor($user);

        return back()->with(
            'success',
            "Google Authenticator enabled for {$user->email}. They must scan the QR code on next login."
        );
    }

    public function disableGoogle2fa(User $user): RedirectResponse
    {
        $this->google2fa->disableFor($user);

        return back()->with(
            'success',
            "Google Authenticator disabled for {$user->email}."
        );
    }
}
