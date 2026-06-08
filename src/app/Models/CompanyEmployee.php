<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyEmployee extends Model
{
    protected $table = 'company_employees';
    protected $fillable = [
        'company_id',
        'user_id',
    ];

    public function company(): belongsTo {
        return $this->belongsTo(Company::class);
    }
}
