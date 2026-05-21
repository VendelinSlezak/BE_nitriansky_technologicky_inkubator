<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Relations\HasOne;

class File extends Model
{
    protected $table = 'files';
    protected $id = 'id';
    protected $fillable = [
        'original_name',
        'path',
        'disk',
        'mime_type',
        'size',
    ];

    public function getUrlAttribute(): string {
        return app(FileService::class)->getUrl($this);
    }

    public function challenge(): HasOne {
        return $this->hasOne(Challenge::class);
    }
}
