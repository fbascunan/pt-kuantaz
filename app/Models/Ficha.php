<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    protected $fillable = ['id_programa', 'monto', 'fecha'];
}

