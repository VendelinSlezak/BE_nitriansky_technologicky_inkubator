<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
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
        'show_logo_image',
        'logo_id'
    ];

    protected $hidden = [
        'user_id',
        'company_address',
        'ico',
        'dic',
        'name_of_contact_person',
        'is_approved_by_admin',
        'show_logo_image',
        'created_at',
        'updated_at',
        'deleted_at',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logo()
    {
        return $this->belongsTo(Attachment::class, 'logo_id');
    }
}
