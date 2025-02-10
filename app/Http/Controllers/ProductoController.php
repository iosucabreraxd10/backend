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
        'imagen' => 'nullable|string', // Se asegura de que la URL de la imagen sea una cadena válida
    ]);

    $producto = new Producto($validatedData);
    $producto->user_id = auth()->id();
    $producto->imagen = $request->imagen ?? null; // Guarda la URL de la imagen si existe
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

public function update(Request $request, $id)
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
        'imagen' => 'nullable|string', // Permitir la actualización de la imagen
    ]);

    $producto = Producto::find($id);
    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    if ($producto->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $producto->update($validatedData);

    return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto], 200);
}

public function destroy($id)
{
    // Buscar el producto
    $producto = Producto::find($id);
    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    // Verificar si el usuario es dueño del producto
    if ($producto->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Eliminar el producto
    $producto->delete();

    return response()->json(['message' => 'Producto eliminado correctamente'], 200);
}
public function destroyByAdmin($id)
{
    try {
        $user = auth()->user();

        if (!$user || $user->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error eliminando el producto'], 500);
    }
}
public function index(Request $request)
{
    $query = Producto::query();

    // Filtrar por categoría si se proporciona
    if ($request->has('categoria')) {
        $query->where('categoria', $request->categoria);
    }

    // Filtrar por género
    if ($request->has('genero') && is_array($request->genero)) {
        $query->whereIn('genero', $request->genero);
    }

    // Filtrar por talla
    if ($request->has('talla') && !empty($request->talla)) {
        $query->whereIn('tamaño', $request->talla);
    }

    // Filtrar por rango de precios correctamente
    if ($request->has('precio') && !empty($request->precio)) {
        if ($request->precio === '500+') {
            $query->where('precio', '>=', 500);
        } else {
            $rangos = explode('-', $request->precio);
            if (count($rangos) == 2) {
                $minPrecio = (float) $rangos[0];
                $maxPrecio = (float) $rangos[1];
                $query->whereBetween('precio', [$minPrecio, $maxPrecio]);
            }
        }
    }

    // Obtener productos filtrados
    $productos = $query->get();

    return response()->json($productos);
}


}
