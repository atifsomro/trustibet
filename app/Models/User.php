<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\HasWallet;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Kyc;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
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
        ];
    }

    protected $guarded = [];

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
}
