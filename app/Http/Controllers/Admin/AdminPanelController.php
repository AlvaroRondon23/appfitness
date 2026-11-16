<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EstadisticasGlobales;
use Illuminate\View\View;

class AdminPanelController extends Controller
{
    public function index(EstadisticasGlobales $estadisticas): View
    {
        return view('admin.panel', ['g' => $estadisticas->resumen()]);
    }
}
