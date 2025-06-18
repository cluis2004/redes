<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/**
 * Ruta principal - Calculadora de pérdida óptica FTTH
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Rutas adicionales para funcionalidades futuras
 */
Route::prefix('api')->group(function () {
    // Aquí se pueden agregar APIs REST para integración con otras aplicaciones
});