<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistroIa;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Supervisión del asistente de IA: solo datos técnicos de uso, nunca el contenido de las conversaciones.
 */
class AdminAsistenteController extends Controller
{
    public function index(Request $request): View
    {
        $desde = now()->subDays(29)->startOfDay();
        $base = fn () => RegistroIa::where('created_at', '>=', $desde);

        $total = $base()->count();
        $ok = $base()->where('resultado', 'ok')->count();

        $porResultado = $base()->selectRaw('resultado, COUNT(*) AS total')
            ->groupBy('resultado')->orderByDesc('total')->pluck('total', 'resultado');

        $porModelo = $base()->where('resultado', 'ok')->whereNotNull('modelo')
            ->selectRaw('modelo, COUNT(*) AS total, ROUND(AVG(duracion_ms)) AS ms')
            ->groupBy('modelo')->orderByDesc('total')->get();

        $filtro = $request->query('resultado');
        $filtro = array_key_exists((string) $filtro, RegistroIa::RESULTADOS) ? $filtro : null;

        $registros = RegistroIa::with('user:id,name')
            ->when($filtro, fn ($q) => $q->where('resultado', $filtro))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.asistente.index', [
            'hoy'          => RegistroIa::where('created_at', '>=', now()->startOfDay())->count(),
            'total'        => $total,
            'exito'        => $total > 0 ? (int) round($ok / $total * 100) : null,
            'usuarios'     => $base()->distinct()->count('user_id'),
            'duracionOk'   => $ok > 0 ? (int) round($base()->where('resultado', 'ok')->avg('duracion_ms')) : null,
            'porResultado' => $porResultado,
            'porModelo'    => $porModelo,
            'registros'    => $registros,
            'filtro'       => $filtro,
        ]);
    }
}
