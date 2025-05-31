<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    protected $fillable = ['id', 'nombre', 'id_programa', 'url', 'categoria', 'descripcion'];

    public function filtro()
    {
        return $this->belongsTo(Filtro::class, 'id_programa', 'id_programa');
    }
}
