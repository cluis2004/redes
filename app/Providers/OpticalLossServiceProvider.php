<?php

namespace App\Providers;

use App\Services\OpticalLossService;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para registrar servicios relacionados con cálculos de pérdida óptica
 * 
 * Implementa el principio de inversión de dependencias (DIP) del patrón SOLID
 */
class OpticalLossServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar OpticalLossService como singleton para reutilización eficiente
        $this->app->singleton(OpticalLossService::class, function ($app) {
            return new OpticalLossService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Aquí se pueden registrar configuraciones adicionales, 
        // observers, listeners, etc. relacionados con pérdida óptica
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [OpticalLossService::class];
    }
}