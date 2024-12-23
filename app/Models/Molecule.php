<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Molecule extends Model
{
    //

    public function interactionsAsMoleculeA(){
        return $this->hasMany(Interaction::class, 'moleculeA_id');
    }

    public function interactionsAsMoleculeB(){
        return $this->hasMany(Interaction::class, 'moleculeB_id');
    }

    public function interactions(){
        return $this->hasMany(Interaction::class);
    }
}
