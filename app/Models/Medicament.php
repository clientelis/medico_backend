<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicament extends Model
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
}
