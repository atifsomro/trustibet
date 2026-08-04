<?php
namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'date_of_birth',
        'country',
        'id_type',
        'id_number',
        'address',
        'city',
        'front_image',
        'back_image',
        'selfie_image',
        'status',
        'rejection_reason',
        'verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'verified_at' => 'datetime',
    ];
    
     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
