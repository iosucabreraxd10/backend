<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    // Definir los campos que son asignables masivamente
    protected $fillable = ['pais'];

    // Si estás usando timestamps
    public $timestamps = true;
    protected $table = 'visitas';

}
