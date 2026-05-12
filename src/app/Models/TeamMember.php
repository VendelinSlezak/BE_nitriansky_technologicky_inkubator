<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $table = 'team_member';
    protected $id = ['team_id', 'student_id'];
    protected $fillable = ['team_id', 'student_id', 'status', 'active_from', 'active_to', 'statuory_declaration_id'];

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}