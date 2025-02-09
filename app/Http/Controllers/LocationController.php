<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function getLocation(Request $request)
    {
        // Obtener la IP del visitante
        $ip = $request->ip();

        // Token de ipinfo.io (reemplaza con tu propio token)
        $token = '5b66f884d4f8f3';

        // Realizar la solicitud a ipinfo.io
        $response = Http::get("http://ipinfo.io/{$ip}/json?token={$token}");

        // Obtener los datos de la respuesta JSON
        $locationData = $response->json();

        // Devolver los datos en formato JSON
        return response()->json($locationData);
    }
}
