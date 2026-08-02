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

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
    
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
}
