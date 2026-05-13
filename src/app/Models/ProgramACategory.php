<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramACategory extends Model
{
    protected $table = 'program_a_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description_of_skills', 'statutory_declaration_id', 'status'];

    public function challenges(): HasMany {
        return $this->hasMany(Challenge::class);
    }

    public function files(): BelongsTo {
        return $this->belongsTo(File::class, 'statutory_declaration_id');
    }

    public function statutory_declaration() {
        return $this->belongsTo(File::class, 'statutory_declaration_id');
    }
}
