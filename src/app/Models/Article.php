<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use SoftDeletes;
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'title', 'perex', 'content', 'published_at'];

    protected $hidden = ['updated_at','deleted_at'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function image(): BelongsTo {
        return $this->belongsTo(Attachment::class);
    }

}

