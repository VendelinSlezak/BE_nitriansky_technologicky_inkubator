<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramACategory extends Model
{
    protected $table = 'program_a_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description_of_skills'];

    public function challenges(): HasMany {
        return $this->hasMany(Challenge::class);
    }
}
