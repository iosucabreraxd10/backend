<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'contraseña' => 'required|string|min:6',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'contraseña' => $request->input('contraseña'),
            'rol' => 'normal',
        ]);

        return response()->json(['message' => 'Usuario registrado exitosamente', 'usuario' => $usuario], 201);
    }

}
