<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramACategory extends Model
{
    use SoftDeletes;
    protected $table = 'program_a_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description_of_skills', 'status'];

    public function challenges(): HasMany {
        return $this->hasMany(Challenge::class);
    }
}
