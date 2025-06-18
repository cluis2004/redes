<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controlador principal para la página de inicio de la aplicación
 */
class HomeController extends Controller
{
    /**
     * Mostrar la página principal con la calculadora de pérdida óptica
     */
    public function index()
    {
        return view('home');
    }
}