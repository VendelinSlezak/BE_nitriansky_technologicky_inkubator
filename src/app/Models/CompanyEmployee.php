<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyEmployee extends Model
{
    use SoftDeletes;
    protected $table = 'company_employees';
    protected $fillable = [
        'company_id',
        'user_id',
    ];
}
