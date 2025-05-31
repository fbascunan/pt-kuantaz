<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filtro extends Model
{
    protected $fillable = ['id_programa', 'tramite', 'min', 'max', 'ficha_id'];

    public function ficha()
    {
        return $this->belongsTo(Ficha::class, 'ficha_id', 'id');
    }
}
