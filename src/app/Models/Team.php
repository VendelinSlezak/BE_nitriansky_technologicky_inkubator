<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    protected $table = 'teams';
    protected $id = 'id';
    protected $fillable = ['name', 'active_from', 'active_to', 'challenge_id', 'proposal_of_implementation_id', 'cover_letter_id'];

    public function challenge() : BelongsTo {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    public function teamMembers() : HasMany {
        return $this->hasMany(TeamMember::class);
    }

    public function students() : BelongsToMany {
        return $this->belongsToMany(Student::class, 'team_member')
            ->withPivot('status', 'active_from', 'active_to', 'statuory_declaration_id')
            ->withTimestamps();
    }

    public function files() : BelongsTo {
        return $this->belongsTo(File::class, 'proposal_of_implementation_id');
    }

    public function proposal_of_implementation() {
        return $this->belongsTo(File::class, 'proposal_of_implementation_id');
    }
}
