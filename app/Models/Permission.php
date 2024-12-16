<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Support\Str;
use App\Models\UserAddRemovePermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
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
     * Relation : Une permission peut appartenir à plusieurs rôles.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }

    /**
     * Relation : Une permission peut être ajoutée ou retirée pour un utilisateur spécifique.
     */
    public function userPermissions()
    {
        return $this->hasMany(UserAddRemovePermission::class);
    }
}
