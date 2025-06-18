<?php

namespace App\DTOs;

/**
 * DTO para representar los datos de entrada del cálculo de pérdida óptica
 */
class OpticalLossCalculationDto
{
    public function __construct(
        public readonly array $splitters = [],
        public readonly int $fusion_count = 0,
        public readonly float $fusion_loss_per_unit = 0.03,
        public readonly int $coupler_count = 0,
        public readonly float $coupler_loss_per_unit = 0.5,
        public readonly float $fiber_distance_km = 0.0,
        public readonly float $fiber_loss_per_km = 0.2,
        public readonly float $design_margin_db = 5.0,
        public readonly float $input_power_dbm = 0.0
    ) {}

    /**
     * Crear instancia desde array de datos
     */
    public static function fromArray(array $data): self
    {
        return new self(
            splitters: $data['splitters'] ?? [],
            fusion_count: (int)($data['fusion_count'] ?? 0),
            fusion_loss_per_unit: (float)($data['fusion_loss_per_unit'] ?? 0.03),
            coupler_count: (int)($data['coupler_count'] ?? 0),
            coupler_loss_per_unit: (float)($data['coupler_loss_per_unit'] ?? 0.5),
            fiber_distance_km: (float)($data['fiber_distance_km'] ?? 0.0),
            fiber_loss_per_km: (float)($data['fiber_loss_per_km'] ?? 0.2),
            design_margin_db: (float)($data['design_margin_db'] ?? 5.0),
            input_power_dbm: (float)($data['input_power_dbm'] ?? 0.0)
        );
    }

    /**
     * Convertir a array
     */
    public function toArray(): array
    {
        return [
            'splitters' => $this->splitters,
            'fusion_count' => $this->fusion_count,
            'fusion_loss_per_unit' => $this->fusion_loss_per_unit,
            'coupler_count' => $this->coupler_count,
            'coupler_loss_per_unit' => $this->coupler_loss_per_unit,
            'fiber_distance_km' => $this->fiber_distance_km,
            'fiber_loss_per_km' => $this->fiber_loss_per_km,
            'design_margin_db' => $this->design_margin_db,
            'input_power_dbm' => $this->input_power_dbm,
        ];
    }
}