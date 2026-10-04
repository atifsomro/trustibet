<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\Google2faService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $request->validate([
            'status' => ['nullable', 'string', Rule::in(AccountStatus::values())],
        ]);

        $query = User::query()->with('wallet');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('account_status', $request->string('status')->toString());
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function suspend(User $user): RedirectResponse
    {
        if ($user->isPermanentlyBlocked()) {
            return back()->with(
                'error',
                "{$user->email} is permanently blocked. Unblock the account before suspending."
            );
        }

        if ($user->isSuspended()) {
            return back()->with('error', "{$user->email} is already suspended.");
        }

        $user->forceFill([
            'account_status' => AccountStatus::SUSPENDED,
        ])->save();

        return back()->with('success', "{$user->email} has been suspended.");
    }

    public function unsuspend(User $user): RedirectResponse
    {
        if (! $user->isSuspended()) {
            return back()->with('error', "{$user->email} is not suspended.");
        }

        $user->forceFill([
            'account_status' => AccountStatus::ACTIVE,
        ])->save();

        return back()->with('success', "{$user->email} has been unsuspended.");
    }

    public function block(User $user): RedirectResponse
    {
        if ($user->isPermanentlyBlocked()) {
            return back()->with('error', "{$user->email} is already permanently blocked.");
        }

        $user->forceFill([
            'account_status' => AccountStatus::BLOCKED,
        ])->save();

        return back()->with(
            'success',
            "{$user->email} has been permanently blocked."
        );
    }

    public function unblock(User $user): RedirectResponse
    {
        if (! $user->isPermanentlyBlocked()) {
            return back()->with('error', "{$user->email} is not permanently blocked.");
        }

        $user->forceFill([
            'account_status' => AccountStatus::ACTIVE,
        ])->save();

        return back()->with('success', "{$user->email} has been unblocked and set to active.");
    }

    /**
     * Start admin-managed authenticator: new secret + show QR for admin to scan.
     */
    public function enableGoogle2fa(User $user): RedirectResponse
    {
        $this->google2fa->enableForAdmin($user);

        return redirect()
            ->route('admin.users.google2fa.setup', $user)
            ->with(
                'success',
                "Scan the QR code for {$user->email} with Google Authenticator, then enter the 6-digit code. After confirmation this user cannot log in until you disable authenticator."
            );
    }

    public function showGoogle2faSetup(User $user): View|RedirectResponse
    {
        if (! $this->google2fa->isManagedByAdmin($user) || ! $this->google2fa->isSetupPending($user)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No admin authenticator setup is pending for this user.');
        }

        return view('admin.users.google2fa-setup', [
            'user' => $user,
            'qrSvg' => $this->google2fa->qrCodeSvg($user),
            'secret' => $user->google2fa_secret,
        ]);
    }

    public function confirmGoogle2faSetup(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        if (! $this->google2fa->isManagedByAdmin($user) || ! $this->google2fa->isSetupPending($user)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No admin authenticator setup is pending for this user.');
        }

        if (! $this->google2fa->verify($user, $request->string('code')->toString())) {
            return back()->with('error', 'Invalid authenticator code. Please try again.');
        }

        $this->google2fa->markConfirmed($user);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                "Authenticator locked for {$user->email}. This user cannot log in until you disable authenticator."
            );
    }

    public function disableGoogle2fa(User $user): RedirectResponse
    {
        $this->google2fa->disableFor($user);

        return back()->with(
            'success',
            "Google Authenticator disabled for {$user->email}. They can log in again."
        );
    }
}
