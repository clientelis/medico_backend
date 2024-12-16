<?php

namespace App\Models;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function (self $model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Relation : Un rôle a plusieurs permissions.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Relation : Un rôle peut être assigné à plusieurs utilisateurs.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
