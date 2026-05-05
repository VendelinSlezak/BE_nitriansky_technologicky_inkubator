<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable {
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'students';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'university',
        'is_accepted_by_admin',
        'team_status',
        'curriculum_vitae_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function curriculumVitae()
    {
        return $this->belongsTo(Attachment::class, 'curriculum_vitae_id');
    }

    public function is_invited_to_the_team(): bool {
        return $this->team_status === 'invited';
    }

    public function is_member_of_the_team(): bool {
        return $this->team_status === 'team_member' || $this->team_status === 'teamleader';
    }
}