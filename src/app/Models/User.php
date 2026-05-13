<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'password',
        'role',
        'email_verified_at',
        'expiration_of_link_for_password_reset',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function articles(): HasMany {
        return $this->hasMany(Article::class);
    }

    public function mentor(): HasOne{
        return $this->hasOne(Mentor::class);
    }

    public function student(): HasOne {
        return $this->hasOne(Student::class);
    }

    public function company(): HasOne {
        return $this->hasOne(Company::class);
    }

    public function challenges(): HasMany {
        return $this->hasMany(Challenge::class);
    }

    public function commision_member_challenges() : BelongsToMany {
        return $this->belongsToMany(Challenge::class, 'commission_members');
    }

    public function company_employees() : BelongsToMany {
        return $this->belongsToMany(User::class, 'company_employees');
    }

    public function isStudent(): bool {
        return $this->role === 'student';
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isMentor(): bool {
        return $this->role === 'mentor';
    }

    public function isCompany(): bool {
        return $this->role === 'company';
    }

    public function isCommitteeMember(): bool {
        return $this->role === 'committee_member';
    }

    public function isWebEditor(): bool {
        return $this->role === 'web_editor';
    }

    public function isCompanyAdmin(): bool {
        return $this->role === 'company_admin';
    }

    public function isCompanyMember(): bool {
        return $this->role === 'company_member';
    }
}
