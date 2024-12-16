<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserAddRemovePermission;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation : Un utilisateur appartient à un rôle.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relation : Un utilisateur peut avoir des permissions supplémentaires ou révoquées.
     */
    public function exceptionalPermissions()
    {
        return $this->hasMany(UserAddRemovePermission::class);
    }
}
