<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mentor extends Model
{
    use SoftDeletes;

    protected $table = 'mentors';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'description', 'experience', 'expertise'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function challenges(): HasMany {
        return $this->hasMany(Challenge::class);
    }
}
