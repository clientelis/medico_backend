<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    //
    protected $fillable = ['moleculeA_id', 'moleculeB_id', 'description'];

    public function moleculeA(){
        return $this->belongsTo(Molecule::class, 'moleculeA_id');
    }

    public function moleculeB(){
        return $this->belongsTo(Molecule::class, 'moleculeB_id');
    }
}
