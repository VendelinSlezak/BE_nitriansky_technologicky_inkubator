<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    protected $table = 'teams';
    protected $id = 'id';
    protected $fillable = ['name', 'active_from', 'active_to', 'challenge_id'];
    protected $hidden = ['pivot'];

    public function challenge() {
        return $this->belongsTo(Challenge::class);
    }

    public function teamMembers() {
        return $this->hasMany(TeamMember::class);
    }

    public function students() : BelongsToMany {
        return $this->belongsToMany(Student::class, 'team_member')
            ->withPivot('status', 'active_from', 'active_to')
            ->withTimestamps();
    }
}
