<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\VisitController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');Route::get('/productos', [ProductoController::class, 'getProductosPorCategoria']);

Route::middleware('auth:sanctum')->put('/user/role', [AuthController::class, 'updateRole']);
Route::middleware('auth:sanctum')->get('/users', [AuthController::class, 'index']);
Route::middleware('auth:sanctum')->put('/user/update', [AuthController::class, 'updateUser']);
Route::delete('/users/{id}', [AuthController::class, 'deleteUser'])->middleware('auth:sanctum');
Route::put('/users/{id}', [AuthController::class, 'updateUser1'])->middleware('auth:sanctum');



Route::middleware(['auth:sanctum', 'verified'])->get('/profile', function () {
    if (!auth()->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Por favor verifica tu correo electrónico antes de acceder al perfil.'], 403);
    }
    return response()->json(['message' => 'Perfil verificado y autenticado']);
    return redirect('http://localhost:5173/novedades');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/profile', function () {
    if (!auth()->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Por favor verifica tu correo electrónico antes de acceder al perfil.'], 403);
    }

    return response()->json(['message' => 'Perfil verificado y autenticado']);
});

// Ruta para obtener información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ruta para mostrar la vista de verificación de correo electrónico
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Ruta para reenviar el enlace de verificación de correo electrónico
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Enlace de verificación enviado.']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// Rutas para el manejo de productos

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/productos', [ProductoController::class, 'store']); // Ruta para almacenar productos
});
Route::middleware('auth:sanctum')->get('/proveedores', [AuthController::class, 'getProveedores']);
Route::get('/productos/proveedor/{proveedorId}', [ProductoController::class, 'getProductosPorProveedor']);
Route::middleware('auth:sanctum')->get('/productos/proveedor', [ProductoController::class, 'getProductosProveedor']);
Route::get('/productos', [ProductoController::class, 'getProductosPorCategoria']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::middleware('auth:sanctum')->post('/carrito', [CarritoController::class, 'agregarAlCarrito']);
Route::middleware('auth:sanctum')->get('/carrito', [CarritoController::class, 'obtenerCarrito']);

Route::get('/productos/filtrados', [ProductoController::class, 'getProductosFiltrados']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/productos/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
});
Route::delete('/productos/admin/{id}', [ProductoController::class, 'destroyByAdmin'])->middleware('auth:sanctum');
Route::delete('/carrito/{id}', [CarritoController::class, 'eliminarDelCarrito'])->middleware('auth:sanctum');

//Localizacion
Route::get('location', [LocationController::class, 'getLocation']);//Estadisticas
Route::get('/estadisticas', [VisitController::class, 'getStats']);
Route::post('/visitas', [VisitController::class, 'store']);
Route::get('/productos', [ProductoController::class, 'index']);