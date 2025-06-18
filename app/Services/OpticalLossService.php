<?php

namespace App\Services;

use App\DTOs\OpticalLossCalculationDto;
use App\DTOs\OpticalLossResultDto;

/**
 * Servicio para calcular pérdidas ópticas en redes FTTH
 * 
 * Este servicio implementa el principio de responsabilidad única (SRP)
 * centralizando toda la lógica de cálculo de pérdidas ópticas.
 */
class OpticalLossService
{
    /**
     * Tipos de splitter predefinidos con sus pérdidas en dB
     */
    private const SPLITTER_TYPES = [
        '1:2' => 3.2,
        '1:4' => 7.2,
        '1:8' => 11.2,
        '1:16' => 14.0,
        '1:32' => 17.5,
        '1:64' => 21.0,
    ];

    /**
     * Calcular pérdida óptica total basada en los parámetros de entrada
     */
    public function calculateTotalLoss(OpticalLossCalculationDto $calculation_data): OpticalLossResultDto
    {
        $breakdown = [];

        // Calcular pérdida de splitters
        $splitter_loss_total = $this->calculateSplitterLoss($calculation_data->splitters, $breakdown);

        // Calcular pérdida de fusiones
        $fusion_loss_total = $this->calculateFusionLoss(
            $calculation_data->fusion_count,
            $calculation_data->fusion_loss_per_unit,
            $breakdown
        );

        // Calcular pérdida de acopladores
        $coupler_loss_total = $this->calculateCouplerLoss(
            $calculation_data->coupler_count,
            $calculation_data->coupler_loss_per_unit,
            $breakdown
        );

        // Calcular pérdida de fibra
        $fiber_loss_total = $this->calculateFiberLoss(
            $calculation_data->fiber_distance_km,
            $calculation_data->fiber_loss_per_km,
            $breakdown
        );

        // Agregar margen de diseño
        $breakdown[] = [
            'component' => 'Margen de Diseño',
            'value' => $calculation_data->design_margin_db,
            'unit' => 'dB',
            'description' => 'Margen de seguridad para el diseño'
        ];

        return OpticalLossResultDto::create(
            splitter_loss_total: $splitter_loss_total,
            fusion_loss_total: $fusion_loss_total,
            coupler_loss_total: $coupler_loss_total,
            fiber_loss_total: $fiber_loss_total,
            design_margin: $calculation_data->design_margin_db,
            input_power_dbm: $calculation_data->input_power_dbm,
            breakdown: $breakdown
        );
    }

    /**
     * Calcular pérdida total de splitters
     */
    private function calculateSplitterLoss(array $splitters, array &$breakdown): float
    {
        $total_loss = 0.0;

        foreach ($splitters as $splitter) {
            $type = $splitter['type'] ?? '';
            $count = (int)($splitter['count'] ?? 0);
            $custom_loss = $splitter['custom_loss'] ?? null;

            if ($count <= 0) continue;

            // Usar pérdida personalizada o la predefinida
            $loss_per_unit = $custom_loss !== null 
                ? (float)$custom_loss 
                : self::SPLITTER_TYPES[$type] ?? 0.0;

            $splitter_total = $loss_per_unit * $count;
            $total_loss += $splitter_total;

            $breakdown[] = [
                'component' => "Splitter {$type}",
                'quantity' => $count,
                'loss_per_unit' => $loss_per_unit,
                'total_loss' => $splitter_total,
                'unit' => 'dB',
                'description' => "Divisor óptico {$type} ({$count} unidades)"
            ];
        }

        return $total_loss;
    }

    /**
     * Calcular pérdida total de fusiones
     */
    private function calculateFusionLoss(int $fusion_count, float $loss_per_unit, array &$breakdown): float
    {
        if ($fusion_count <= 0) return 0.0;

        $total_loss = $fusion_count * $loss_per_unit;

        $breakdown[] = [
            'component' => 'Fusiones',
            'quantity' => $fusion_count,
            'loss_per_unit' => $loss_per_unit,
            'total_loss' => $total_loss,
            'unit' => 'dB',
            'description' => "Empalmes por fusión ({$fusion_count} unidades)"
        ];

        return $total_loss;
    }

    /**
     * Calcular pérdida total de acopladores
     */
    private function calculateCouplerLoss(int $coupler_count, float $loss_per_unit, array &$breakdown): float
    {
        if ($coupler_count <= 0) return 0.0;

        $total_loss = $coupler_count * $loss_per_unit;

        $breakdown[] = [
            'component' => 'Acopladores',
            'quantity' => $coupler_count,
            'loss_per_unit' => $loss_per_unit,
            'total_loss' => $total_loss,
            'unit' => 'dB',
            'description' => "Conectores y acopladores ({$coupler_count} unidades)"
        ];

        return $total_loss;
    }

    /**
     * Calcular pérdida total de fibra óptica
     */
    private function calculateFiberLoss(float $distance_km, float $loss_per_km, array &$breakdown): float
    {
        if ($distance_km <= 0) return 0.0;

        $total_loss = $distance_km * $loss_per_km;

        $breakdown[] = [
            'component' => 'Fibra Óptica',
            'distance_km' => $distance_km,
            'loss_per_km' => $loss_per_km,
            'total_loss' => $total_loss,
            'unit' => 'dB',
            'description' => "Atenuación por distancia ({$distance_km} km)"
        ];

        return $total_loss;
    }

    /**
     * Obtener tipos de splitter disponibles
     */
    public function getAvailableSplitterTypes(): array
    {
        return self::SPLITTER_TYPES;
    }

    /**
     * Validar que la potencia de salida esté dentro de rangos aceptables
     */
    public function validateOutputPower(float $output_power_dbm): array
    {
        $warnings = [];

        // Rangos típicos para FTTH
        if ($output_power_dbm < -28) {
            $warnings[] = 'Potencia de salida muy baja. Puede haber problemas de conectividad.';
        }

        if ($output_power_dbm > -8) {
            $warnings[] = 'Potencia de salida muy alta. Verificar cálculos.';
        }

        if ($output_power_dbm < -25 && $output_power_dbm >= -28) {
            $warnings[] = 'Potencia de salida en el límite inferior. Considerar optimizar el diseño.';
        }

        return $warnings;
    }
}