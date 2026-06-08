<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Milestone extends Model
{
    use SoftDeletes;
    protected $table = 'milestones';
    protected $id = 'id';
    protected $fillable = ['challenge_id', 'title', 'description', 'comment', 'date_of_reasisation', 'is_finished'];
    protected $hidden = ['challenge_id', 'created_at', 'updated_at', 'deleted_at'];

    public function challenge() {
        return $this->belongsTo(Challenge::class);
    }
}
