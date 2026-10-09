<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AccountStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\HasWallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Kyc;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'google2fa_secret'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasWallet;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_set_at' => 'datetime',
            'notification_preferences' => 'array',
            'last_login_at' => 'datetime',
            'google2fa_enabled' => 'boolean',
            'google2fa_secret' => 'encrypted',
            'google2fa_confirmed_at' => 'datetime',
            'google2fa_managed_by_admin' => 'boolean',
            'account_status' => AccountStatus::class,
        ];
    }

    protected $guarded = [];

    public function canLogin(): bool
    {
        return ($this->account_status ?? AccountStatus::ACTIVE)->canLogin();
    }

    public function isSuspended(): bool
    {
        return $this->account_status === AccountStatus::SUSPENDED;
    }

    public function isPermanentlyBlocked(): bool
    {
        return $this->account_status === AccountStatus::BLOCKED;
    }

    public function accountRestrictionMessage(): string
    {
        return match ($this->account_status) {
            AccountStatus::SUSPENDED => 'Your account has been suspended. Please contact support.',
            AccountStatus::BLOCKED => 'Your account has been permanently blocked.',
            default => 'Your account is not allowed to sign in.',
        };
    }

    function store($request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->country_id = $request->country_id;
        $user->password = Hash::make($request->password);
        $user->referral_code = self::generateUniqueCode('referral_code');
        $user->verification_code = self::generateUniqueCode('verification_code');
        $user->save();
        return $user->id;
    }

    public static function generateUniqueCode($column)
    {
        do {
            $code = strtoupper(Str::random(6));
            // Replace confusing letters
            $code = preg_replace('/[^A-Z0-9]/', '', $code);
        } while (self::where($column, $code)->exists());
        return $code;
    }

    /**
     * Generate a unique username from an email/base string.
     * Used when creating accounts from Google (or any OAuth) sign-in,
     * where no username is supplied by the provider.
     */
    public static function generateUniqueUsernameFrom(string $base): string
    {
        $slug = Str::slug(Str::before($base, '@'), '');
        $slug = $slug !== '' ? $slug : 'user';
        $slug = Str::limit($slug, 15, '');

        $username = $slug;
        $attempt = 0;

        while (self::query()->where('username', $username)->exists()) {
            $attempt++;
            $username = $slug . strtolower(Str::random(4));
            // Safety valve, should never realistically loop long
            if ($attempt > 20) {
                $username = $slug . uniqid();
                break;
            }
        }

        return $username;
    }

    /**
     * Determine whether this account was created purely via an OAuth
     * provider (e.g. Google) and never had a locally-set password.
     */
    public function isSocialAccount(): bool
    {
        return !empty($this->provider) && !empty($this->google_id);
    }

    /**
     * A local account always has a chosen password.
     * A Google-only account does not until the user sets one.
     */
    public function hasChosenPassword(): bool
    {
        return $this->password_set_at !== null || ! $this->isSocialAccount();
    }

    /**
     * @return array{email: bool, sms: bool, offers: bool, winner: bool, transaction: bool}
     */
    public function notificationPreferences(): array
    {
        $saved = is_array($this->notification_preferences) ? $this->notification_preferences : [];

        return array_merge([
            'email' => true,
            'sms' => false,
            'offers' => true,
            'winner' => true,
            'transaction' => true,
        ], $saved);
    }

    public function recordLogin(?string $userAgent): void
    {
        $this->forceFill([
            'last_login_at' => now(),
            'last_login_user_agent' => $userAgent !== null && $userAgent !== ''
                ? mb_substr($userAgent, 0, 512)
                : null,
        ])->save();
    }

    public function lastLoginLabel(): ?string
    {
        if ($this->last_login_at === null) {
            return null;
        }

        return $this->last_login_at->format('d M Y').' - '.self::describeUserAgent($this->last_login_user_agent);
    }

    public static function describeUserAgent(?string $userAgent): string
    {
        $ua = $userAgent ?? '';

        if ($ua === '') {
            return 'Unknown device';
        }

        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Browser',
        };

        $os = match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS') || str_contains($ua, 'Macintosh') => 'macOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone'), str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => '',
        };

        return trim($browser.' '.$os);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
    
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
    public function kyc(): HasOne{
        return $this->hasOne(Kyc::class);
    }

    public function lotteryTickets(): HasMany
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function lotteryWins(): HasMany
    {
        return $this->hasMany(LotteryWinner::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(UserInvestment::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referralEarnings(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referralAsInvitee(): HasOne
    {
        return $this->hasOne(Referral::class, 'referred_user_id');
    }

    public function hasApprovedDeposit(): bool
    {
        return $this->deposits()->approved()->exists();
    }
}
