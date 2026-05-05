<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $table = 'milestones';
    protected $id = 'id';
    protected $fillable = ['challenge_id', 'title', 'description', 'comment', 'date_of_reasisation', 'is_finished'];

    public function challenge() {
        return $this->belongsTo(Challenge::class);
    }
}
