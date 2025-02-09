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
public function getProductosFiltrados(Request $request)
{
    $request->validate([
        'color' => 'nullable|string',
        'tamaño' => 'nullable|string|in:xs,s,m,l,xl,xxl,xxxl',
        'precioRange' => 'nullable|integer|between:1,6', // Validar que el precioRange esté entre 1 y 6
    ]);

    $query = Producto::query();

    // Filtrado por color
    if ($request->has('color') && $request->color != null) {
        $query->where('color', $request->color);
    }

    // Filtrado por tamaño
    if ($request->has('tamaño') && $request->tamaño != null) {
        $query->where('tamaño', $request->tamaño);
    }

    // Filtrado por precio
    if ($request->has('precioRange') && $request->precioRange != null) {
        $range = $request->precioRange;
        switch ($range) {
            case 1:
                $query->whereBetween('precio', [0, 20]);
                break;
            case 2:
                $query->whereBetween('precio', [20, 50]);
                break;
            case 3:
                $query->whereBetween('precio', [50, 100]);
                break;
            case 4:
                $query->whereBetween('precio', [100, 200]);
                break;
            case 5:
                $query->whereBetween('precio', [200, 500]);
                break;
            case 6:
                $query->where('precio', '>', 500);
                break;
            default:
                return response()->json(['message' => 'Rango de precio inválido'], 400);
        }
        
    }

    $productos = $query->get();
    return response()->json($productos);
}
public function update(Request $request, $id)
{
    // Validar los datos entrantes
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

    // Buscar el producto
    $producto = Producto::find($id);
    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    // Verificar si el usuario es dueño del producto
    if ($producto->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Actualizar el producto
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

        // Filtro por categoría (opcional)
        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Filtro por color
        if ($request->has('color')) {
            $query->where('color', $request->color);
        }

        // Filtro por talla
        if ($request->has('tamaño')) {
            $query->where('tamaño', $request->tamaño);
        }

        // Filtro por género
        if ($request->has('genero')) {
            $generos = explode(',', $request->genero);
            $query->whereIn('genero', $generos);
        }

        // Filtro por rango de precio
        if ($request->has('precioMin')) {
            $query->where('precio', '>=', $request->precioMin);
        }

        if ($request->has('precioMax')) {
            $query->where('precio', '<=', $request->precioMax);
        }

        $productos = $query->get();

        return response()->json($productos);
    }


    
}
