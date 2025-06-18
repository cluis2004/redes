<?php

namespace App\DTOs;

/**
 * DTO para representar el resultado del cálculo de pérdida óptica
 */
class OpticalLossResultDto
{
    public function __construct(
        public readonly float $splitter_loss_total = 0.0,
        public readonly float $fusion_loss_total = 0.0,
        public readonly float $coupler_loss_total = 0.0,
        public readonly float $fiber_loss_total = 0.0,
        public readonly float $design_margin = 0.0,
        public readonly float $total_loss_db = 0.0,
        public readonly float $output_power_dbm = 0.0,
        public readonly array $breakdown = []
    ) {}

    /**
     * Crear instancia con desglose detallado
     */
    public static function create(
        float $splitter_loss_total,
        float $fusion_loss_total,
        float $coupler_loss_total,
        float $fiber_loss_total,
        float $design_margin,
        float $input_power_dbm,
        array $breakdown = []
    ): self {
        $total_loss_db = $splitter_loss_total + $fusion_loss_total + $coupler_loss_total + $fiber_loss_total + $design_margin;
        $output_power_dbm = $input_power_dbm - $total_loss_db;

        return new self(
            splitter_loss_total: $splitter_loss_total,
            fusion_loss_total: $fusion_loss_total,
            coupler_loss_total: $coupler_loss_total,
            fiber_loss_total: $fiber_loss_total,
            design_margin: $design_margin,
            total_loss_db: $total_loss_db,
            output_power_dbm: $output_power_dbm,
            breakdown: $breakdown
        );
    }

    /**
     * Convertir a array para JSON
     */
    public function toArray(): array
    {
        return [
            'splitter_loss_total' => $this->splitter_loss_total,
            'fusion_loss_total' => $this->fusion_loss_total,
            'coupler_loss_total' => $this->coupler_loss_total,
            'fiber_loss_total' => $this->fiber_loss_total,
            'design_margin' => $this->design_margin,
            'total_loss_db' => $this->total_loss_db,
            'output_power_dbm' => $this->output_power_dbm,
            'breakdown' => $this->breakdown,
        ];
    }
}