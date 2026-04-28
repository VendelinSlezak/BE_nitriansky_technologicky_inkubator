<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'link_for_password_reset',
        'expiration_of_link_for_password_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'email',
        'password',
        'role',
        'email_verified_at',
        'expiration_of_link_for_password_reset',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var list<string, string>
     */
    protected $casts =  [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'expiration_of_link_for_password_reset' => 'datetime',
    ];

    public function isStudent(): bool {
        return $this->role === 'student';
    }
}