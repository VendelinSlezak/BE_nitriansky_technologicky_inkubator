<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Challenge extends Model
{
    protected $table = 'challenges';
    protected $primaryKey = 'id';

    protected $fillable = [
        'program',
        'name',
        'automatically_create_team_after_approval',
        'description',
        'reward',
        'status',
        'comment_of_commission',
        'final_assessment',
        'program_a_category'
    ];

    protected $hidden = [
        'user_id',
        'mentor_id',
        'automatically_create_team_after_approval',
        'proposal_file_id',
        'status',
        'comment_of_commission',
        'final_assessment',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function mentors(): BelongsTo {
        return $this->belongsTo(Mentor::class);
    }

    public function users(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function attachments(): MorphMany {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function program_a_categories(): BelongsTo {
        return $this->belongsTo(ProgramACategory::class, 'program_a_category_id');
    }
}
