<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMember extends Pivot
{
    protected $table = 'team_member';
    protected $id = ['team_id', 'student_id'];
    protected $fillable = ['team_id', 'student_id', 'status', 'active_from', 'active_to', 'statuory_declaration_id'];

    public function team() {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function student() {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function statuory_declaration() {
        return $this->belongsTo(File::class, 'statuory_declaration_id');
    }
}