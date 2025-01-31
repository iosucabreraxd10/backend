<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
{
    try {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'contraseña' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->name = $validated['nombre'];
        $user->email = $validated['correo'];
        $user->password = bcrypt($validated['contraseña']);
        $user->rol = $request->input('rol', 'normal'); // opcional
        $user->save();

        
        return response()->json(['message' => 'Usuario registrado con éxito'], 201);
    } catch (\Exception $e) {
        Log::error('Error al registrar usuario: ' . $e->getMessage());
        return response()->json(['error' => 'Error al registrar usuario'], 500);
    }
}

    public function login(Request $request)
    {
        try{
            $request->validate([
                'correo' => 'required|email',
                'contraseña' => 'required',
            ]);

            $user = User::where('email', $request->correo)->first();

            if (!$user || !Hash::check($request->contraseña, $user->password)) {
                return response()->json(['message' => 'Credenciales incorrectas'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
                'user' => $user,
            ]);
        }catch(ValidationException){
            
        }
        
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
    public function updateRole(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'rol' => 'required|string|in:proveedor,cliente', // Agrega más roles según sea necesario
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Actualizar el rol del usuario
        $user->rol = $request->rol;
        $user->save();

        return response()->json(['message' => 'Rol actualizado con éxito', 'user' => $user], 200);
    }
    public function index(Request $request)
    {
        try {
            // Obtener los usuarios (limitar los campos que necesitas)
            $users = User::select('id', 'name', 'email', 'rol')->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los usuarios', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateUser(Request $request)
{
    try {
        $user = Auth::user();

        // Validar los datos del usuario
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
        ]);

        // Actualizar los datos del usuario
        $user->update($request->only(['name', 'email']));

        return response()->json(['message' => 'Usuario actualizado con éxito', 'user' => $user], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al actualizar los datos del usuario', 'error' => $e->getMessage()], 500);
    }
}

public function verifyEmail(Request $request)
    {
        try {
            $user = User::find($request->id);
    
            if (!$user) {
                return redirect('http://localhost:5173/email-verification/error?type=user-not-found');
            }
    
            if (!hash_equals(sha1($user->getEmailForVerification()), $request->hash)) {
                return redirect('http://localhost:5173/email-verification/error?type=invalid-verification-code');
            }
    
            if ($user->hasVerifiedEmail()) {
                return redirect('http://localhost:5173/email-verification/error?type=already-verified');
            }
    
            $user->markEmailAsVerified();
    
            return redirect('http://localhost:5173/novedades');
        } catch (\Exception $e) {
            return redirect('http://localhost:5173/email-verification/error?type=general-error');
        }
    }


public function deleteUser($id)
{
    try {
        $user = User::findOrFail($id);

        // Eliminar el usuario
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado con éxito',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al eliminar el usuario',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function updateUser1(Request $request, $id)
{
    try {
        // Buscar al usuario por ID
        $user = User::findOrFail($id);

        // Validar los datos recibidos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'rol' => 'nullable|string|in:normal,administrador,proveedor', // Opcional si necesitas editar el rol
        ]);

        // Actualizar los datos del usuario
        $user->update($request->only(['name', 'email', 'rol']));

        return response()->json([
            'message' => 'Usuario actualizado con éxito',
            'user' => $user
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar el usuario',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'materiales' => 'required|string|max:255',
            'cuidados' => 'required|string|max:255',
            'tamaño' => 'required|in:xs,s,m,l,xl,xxl,xxxl',
            'color' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|string|max:255',
            'composicion' => 'required|string|max:255',
            'stock' => 'required|integer',
            'categoria' => 'required|in:futbol,baloncesto,running,natacion,surf,ciclismo,skateboarding,fitness,tenis,boxeo',
            'genero' => 'required|in:hombre,mujer,unisex,niño,niña',
            'tipo' => 'required|in:camiseta,pantalon',
            'user_id' => 'required|exists:users,id',
        ]);
    
    
        // Crear el producto en la base de datos
        Producto::create([
            'nombre' => $validated['nombre'],
            'materiales' => $validated['materiales'],
            'cuidados' => $validated['cuidados'],
            'tamaño' => $validated['tamaño'],
            'color' => $validated['color'],
            'descripcion' => $validated['descripcion'],
            'precio' => $validated['precio'],
            'composicion' => $validated['composicion'],
            'stock' => $validated['stock'],
            'categoria' => $validated['categoria'],
            'genero' => $validated['genero'],
            'tipo' => $validated['tipo'],
            'user_id' => $validated['user_id'],
        ]);
    
        return response()->json(['message' => 'Producto creado exitosamente'], 201);
    }
    public function getProveedores(Request $request)
{
    try {
        // Obtener solo los usuarios con rol 'proveedor'
        $proveedores = User::where('rol', 'proveedor')
            ->select('id', 'name', 'email')
            ->get();

        return response()->json($proveedores);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al obtener los proveedores', 'message' => $e->getMessage()], 500);
    }
}





}
