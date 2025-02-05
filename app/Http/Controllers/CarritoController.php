<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;

class CarritoController extends Controller
{
    public function agregarAlCarrito(Request $request)
{
    // Verificar que el usuario está autenticado
    $user = $request->user();  // Esto debería devolver el usuario autenticado
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    // Validar los datos recibidos
    $validated = $request->validate([
        'producto_id' => 'required|exists:productos,id',
    ]);

    if (!$validated) {
        return response()->json(['error' => 'Datos inválidos o faltantes'], 400);
    }

    try {
        // Crear una nueva entrada en el carrito, usando el ID del usuario autenticado
        $carrito = Carrito::create([
            'producto_id' => $request->producto_id,
            'usuario_id' => $user->id, // Usar el ID del usuario autenticado
        ]);

        return response()->json([
            'message' => 'Producto agregado al carrito exitosamente',
            'carrito' => $carrito,
        ], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al agregar al carrito: ' . $e->getMessage()], 500);
    }
}
public function obtenerCarrito(Request $request)
    {
        // Verificar que el usuario está autenticado
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            // Obtener los productos del carrito del usuario autenticado
            $carrito = Carrito::where('usuario_id', $user->id)
                              ->with('producto')  // Esto carga los datos del producto relacionado
                              ->get();

            // Verificar si el carrito está vacío
            if ($carrito->isEmpty()) {
                return response()->json(['message' => 'Tu carrito está vacío.']);
            }

            // Devolver los productos del carrito
            return response()->json($carrito);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el carrito: ' . $e->getMessage()], 500);
        }
    }



  
}
