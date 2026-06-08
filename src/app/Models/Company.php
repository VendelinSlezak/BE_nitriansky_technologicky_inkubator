<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;
    protected $table = 'companies';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'company_address',
        'description',
        'ico',
        'dic',
        'category',
        'name_of_contact_person',
        'is_approved_by_admin',
        'logo_id'
    ];

    protected $hidden = [
        'company_address',
        'ico',
        'dic',
        'name_of_contact_person',
        'is_approved_by_admin',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logo()
    {
        return $this->belongsTo(File::class, 'logo_id');
    }

    public function company_employees() : BelongsToMany {
        return $this->belongsToMany(User::class, 'company_employees');
    }
}
