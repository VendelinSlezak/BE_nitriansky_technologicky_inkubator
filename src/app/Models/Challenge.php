<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Challenge extends Model
{
    protected $table = 'challenges';
    protected $primaryKey = 'id';

    protected $fillable = [
        'program',
        'user_id',
        'mentor_id',
        'name',
        'automatically_create_team_after_approval',
        'description',
        'reward',
        'status',
        'final_assessment',
        'program_a_category_id',
        'proposal_file_id',
        'commission_id',
    ];

    public function mentors(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): BelongsTo
    {
        return $this->belongsTo(File::class, 'proposal_file_id');
    }

    public function program_a_categories(): BelongsTo
    {
        return $this->belongsTo(ProgramACategory::class, 'program_a_category_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function proposal_file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'proposal_file_id');
    }

    public function attached_team() {
        return $this->hasOne(Team::class)
            ->whereNotNull('active_from')
            ->latestOfMany();
    }

    public function commission_members() : BelongsToMany {
        return $this->belongsToMany(User::class, 'commission_members');
    }
}