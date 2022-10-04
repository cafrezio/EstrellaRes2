<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendicione extends Model
{
    use HasFactory;

    public function gastos(){
        return $this->hasMany('App\Models\Rendiciongasto');
    }
}
