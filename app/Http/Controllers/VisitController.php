<?php
namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    // Guardar una nueva visita
    public function store(Request $request)
{
    try {
        \Log::info('Datos recibidos en la API:', $request->all());

        $pais = $request->input('pais');

        if (!$pais) {
            \Log::error('Falta el país en la solicitud.');
            return response()->json(['error' => 'Falta el país'], 400);
        }

        $visita = Visit::create(['pais' => $pais]);

        \Log::info('Visita guardada:', ['id' => $visita->id]);

        return response()->json(['message' => 'Visita guardada'], 200);
    } catch (\Exception $e) {
        \Log::error('Error al guardar la visita: ' . $e->getMessage());
        return response()->json(['error' => 'Error al guardar la visita', 'message' => $e->getMessage()], 500);
    }
}


    // Obtener las estadísticas de visitas agrupadas por país
    public function getStats()
    {
        try {
            $visitas = Visit::select('pais', DB::raw('count(*) as count'))
                ->groupBy('pais')
                ->orderByDesc('count')
                ->get();
    
            // Loguear el resultado de la consulta para asegurarnos de que la consulta está funcionando
            \Log::info('Visitas:', $visitas->toArray());
    
            return response()->json($visitas);
        } catch (\Exception $e) {
            // Capturamos el error y logueamos el mensaje
            \Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las estadísticas', 'message' => $e->getMessage()], 500);
        }
    }
    
}
