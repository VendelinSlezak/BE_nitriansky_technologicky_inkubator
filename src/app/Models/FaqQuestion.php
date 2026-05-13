<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqQuestion extends Model
{
    protected $table = 'faq_questions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'type', // 'A' or 'B'
        'question',
        'answer',
    ];
}
