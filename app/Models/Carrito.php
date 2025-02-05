<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carrito';

    protected $fillable = [
        'producto_id',
        'usuario_id',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id'); // Asegúrate de que el nombre de la columna coincida
    }

    // Relación con Usuario (si necesitas los detalles del usuario también)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
