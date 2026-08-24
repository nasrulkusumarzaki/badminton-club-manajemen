<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'foto', // <-- Tambahkan ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // <-- Perbaiki typo (sebelumnya remmember_token)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Ensure passwords are hashed when set on the model.
     *
     * This mutator makes assignment idempotent: if a hashed value is provided
     * (e.g. controllers already call Hash::make), it will be stored as-is.
     * If a plain password is provided, it will be hashed here.
     *
     * @param  string|null  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return;
        }

        // Detect common hash prefixes for bcrypt and argon variants.
        if (preg_match('/^\$2y\$|^\$2a\$|^\$argon2i\$|^\$argon2id\$/', $value)) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::make($value);
    }
}