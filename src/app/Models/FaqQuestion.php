<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqQuestion extends Model
{
    use SoftDeletes;
    protected $table = 'faq_questions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'type', // 'A' or 'B'
        'question',
        'answer',
    ];
}
