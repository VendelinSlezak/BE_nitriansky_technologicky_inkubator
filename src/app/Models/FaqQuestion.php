<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqQuestion extends Model
{
    protected $table = 'faq_questions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'question',
        'answer',
        'type', // 'A' or 'B'
    ];
}
