<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'materiales' => 'required|string|max:255',
            'cuidados' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'composicion' => 'required|string|max:255',
            'stock' => 'required|integer',
            'genero' => 'required|in:hombre,mujer,unisex,niño,niña',
            'tipo' => 'required|in:camiseta,pantalon',
            'tamaño' => 'required|in:xs,s,m,l,xl,xxl,xxxl',
            'categoria' => 'required|in:futbol,baloncesto,running,natacion,surf,ciclismo,skateboarding,fitness,tenis,boxeo',
        ]);

        $producto = new Producto($validatedData);
        $producto->user_id = auth()->id();
        $producto->save();

        return response()->json(['message' => 'Producto creado exitosamente', 'producto' => $producto], 201);
    }

    public function getProductosPorProveedor($proveedorId)
    {
        // Buscar productos por proveedorId (relacionado con user_id)
        $productos = Producto::where('user_id', $proveedorId)->get();

        if ($productos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron productos para este proveedor'], 404);
        }

        return response()->json(['productos' => $productos], 200);
    }
    public function getProductosProveedor()
    {
        $productos = Producto::where('user_id', auth()->id())->get(); // Obtener productos del proveedor autenticado
        return response()->json($productos);
    }
    public function getProductosPorCategoria(Request $request)
{
    $categoria = $request->query('categoria');

    // Asegúrate de que la categoría exista y esté en el listado de categorías permitidas
    $categoriasPermitidas = ['futbol', 'baloncesto', 'running', 'natacion', 'surf', 'ciclismo', 'skateboarding', 'fitness', 'tenis', 'boxeo'];
    
    if (!in_array($categoria, $categoriasPermitidas)) {
        return response()->json(['message' => 'Categoría no válida.'], 400);
    }

    $productos = Producto::where('categoria', $categoria)->get();
    return response()->json($productos);
}
public function show($id)
{
    // Buscar el producto por su ID
    $producto = Producto::find($id);

    // Si no se encuentra el producto, devuelve un error 404
    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    // Si el producto se encuentra, devolverlo en formato JSON
    return response()->json($producto);
}



    
}
