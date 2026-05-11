<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->belongsTo(File::class, 'curriculum_vitae_id');
    }

    public function teams() : BelongsToMany {
        return $this->belongsToMany(Team::class, 'team_member')
            ->withPivot('status', 'active_from', 'active_to', 'statuory_declaration_id')
            ->withTimestamps();
    }

    public function is_invited_to_the_team(): bool {
        return $this->team_status === 'invited';
    }

    public function is_member_of_the_team(): bool {
        return $this->team_status === 'team_member' || $this->team_status === 'teamleader';
    }

    public function active_team()
    {
        $now = now();
        $pivotTable = $this->teams()->getTable(); 

        return $this->teams()
            ->select(   'team_member.status',
                        'team_member.active_from',
                        'team_member.active_to',
                        'team_member.statuory_declaration_id',
                        'teams.id',
                        'teams.name',
                        'teams.challenge_id',
                        'teams.active_from',
                        'teams.proposal_of_implementation_id')
            ->where(function ($query) use ($now, $pivotTable) {
                $query->whereNull("{$pivotTable}.active_to")
                    ->orWhere("{$pivotTable}.active_to", '>=', $now);
            })
            ->limit(1);
    }
}