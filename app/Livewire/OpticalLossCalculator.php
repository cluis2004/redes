<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OpticalLossService;
use App\DTOs\OpticalLossCalculationDto;
use App\DTOs\OpticalLossResultDto;

/**
 * Componente Livewire para la calculadora de pérdida óptica
 * 
 * Este componente maneja la interfaz de usuario y la lógica de presentación,
 * delegando los cálculos al OpticalLossService (separación de responsabilidades).
 */
class OpticalLossCalculator extends Component
{
    // Propiedades de entrada del usuario
    public $input_power_dbm = 5.0;
    public $fusion_count = 4;
    public $fusion_loss_per_unit = 0.03;
    public $coupler_count = 2;
    public $coupler_loss_per_unit = 0.5;
    public $fiber_distance_km = 0.75; // 750 metros = 0.75 km
    public $fiber_loss_per_km = 0.2;
    public $design_margin_db = 0.0; // Sin margen por defecto según imagen

    // Propiedades para splitters dinámicos
    public $splitters = [];

    // Propiedades calculadas como arrays simples (compatible con Livewire)
    public $splitter_loss_total = 0.0;
    public $fusion_loss_total = 0.0;
    public $coupler_loss_total = 0.0;
    public $fiber_loss_total = 0.0;
    public $design_margin = 0.0;
    public $total_loss_db = 0.0;
    public $output_power_dbm = 0.0;
    public $breakdown = [];
    public $warnings = [];

    // Variables de estado de la UI
    public $show_breakdown = true;
    public $active_tab = 'visual';

    protected OpticalLossService $optical_loss_service;

    public function boot(OpticalLossService $optical_loss_service)
    {
        $this->optical_loss_service = $optical_loss_service;
    }

    public function mount()
    {
        // Inicializar con splitters por defecto
        $this->splitters = [
            [
                'id' => uniqid(),
                'type' => '1:2',
                'count' => 1,
                'custom_loss' => null,
                'use_custom' => false
            ]
        ];

        $this->calculateLoss();
    }

    /**
     * Agregar nuevo splitter a la lista
     */
    public function addSplitter()
    {
        $this->splitters[] = [
            'id' => uniqid(),
            'type' => '1:2',
            'count' => 1,
            'custom_loss' => null,
            'use_custom' => false
        ];

        $this->calculateLoss();
    }

    /**
     * Remover splitter por ID
     */
    public function removeSplitter($splitter_id)
    {
        $this->splitters = array_filter(
            $this->splitters,
            fn($splitter) => $splitter['id'] !== $splitter_id
        );

        $this->splitters = array_values($this->splitters);
        $this->calculateLoss();
    }

    /**
     * Actualizar splitter específico
     */
    public function updatedSplitters()
    {
        $this->calculateLoss();
    }

    /**
     * Listeners para actualizaciones automáticas cuando cambian las propiedades
     */
    public function updated($property_name)
    {
        // Recalcular automáticamente cuando cualquier propiedad cambie
        if (in_array($property_name, [
            'input_power_dbm', 'fusion_count', 'fusion_loss_per_unit',
            'coupler_count', 'coupler_loss_per_unit', 'fiber_distance_km',
            'fiber_loss_per_km', 'design_margin_db'
        ])) {
            $this->calculateLoss();
        }
    }

    /**
     * Calcular pérdida óptica usando el servicio
     */
    public function calculateLoss()
    {
        try {
            $calculation_data = OpticalLossCalculationDto::fromArray([
                'splitters' => $this->splitters,
                'fusion_count' => $this->fusion_count,
                'fusion_loss_per_unit' => $this->fusion_loss_per_unit,
                'coupler_count' => $this->coupler_count,
                'coupler_loss_per_unit' => $this->coupler_loss_per_unit,
                'fiber_distance_km' => $this->fiber_distance_km,
                'fiber_loss_per_km' => $this->fiber_loss_per_km,
                'design_margin_db' => $this->design_margin_db,
                'input_power_dbm' => $this->input_power_dbm,
            ]);

            $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);
            
            // Asignar valores individuales en lugar del objeto completo
            $this->splitter_loss_total = $result->splitter_loss_total;
            $this->fusion_loss_total = $result->fusion_loss_total;
            $this->coupler_loss_total = $result->coupler_loss_total;
            $this->fiber_loss_total = $result->fiber_loss_total;
            $this->design_margin = $result->design_margin;
            $this->total_loss_db = $result->total_loss_db;
            $this->output_power_dbm = $result->output_power_dbm;
            $this->breakdown = $result->breakdown;
            
            $this->warnings = $this->optical_loss_service->validateOutputPower($this->output_power_dbm);

        } catch (\Exception $e) {
            $this->warnings = ['Error en el cálculo: ' . $e->getMessage()];
            $this->resetCalculationResults();
        }
    }

    /**
     * Restablecer resultados de cálculo
     */
    private function resetCalculationResults()
    {
        $this->splitter_loss_total = 0.0;
        $this->fusion_loss_total = 0.0;
        $this->coupler_loss_total = 0.0;
        $this->fiber_loss_total = 0.0;
        $this->design_margin = 0.0;
        $this->total_loss_db = 0.0;
        $this->output_power_dbm = 0.0;
        $this->breakdown = [];
    }

    /**
     * Alternar visualización del desglose
     */
    public function toggleBreakdown()
    {
        $this->show_breakdown = !$this->show_breakdown;
    }

    /**
     * Cambiar pestaña activa
     */
    public function setActiveTab($tab)
    {
        $this->active_tab = $tab;
    }

    /**
     * Restablecer valores por defecto
     */
    public function resetToDefaults()
    {
        $this->input_power_dbm = 5.0;
        $this->fusion_count = 4;
        $this->fusion_loss_per_unit = 0.03;
        $this->coupler_count = 2;
        $this->coupler_loss_per_unit = 0.5;
        $this->fiber_distance_km = 0.75; // 750 metros
        $this->fiber_loss_per_km = 0.2;
        $this->design_margin_db = 0.0;

        $this->splitters = [
            [
                'id' => uniqid(),
                'type' => '1:2',
                'count' => 1,
                'custom_loss' => null,
                'use_custom' => false
            ]
        ];

        $this->calculateLoss();
    }

    /**
     * Obtener tipos de splitter disponibles
     */
    public function getAvailableSplitterTypes()
    {
        return $this->optical_loss_service->getAvailableSplitterTypes();
    }

    /**
     * Obtener color para el indicador de potencia de salida
     */
    public function getOutputPowerColor()
    {
        if ($this->output_power_dbm < -28) return 'red';
        if ($this->output_power_dbm < -25) return 'yellow';
        if ($this->output_power_dbm <= -8) return 'green';
        
        return 'red'; // Muy alto
    }

    /**
     * Verificar si hay resultados calculados
     */
    public function hasResults()
    {
        return $this->total_loss_db > 0 || $this->output_power_dbm != 0;
    }

    public function render()
    {
        return view('livewire.optical-loss-calculator', [
            'splitter_types' => $this->getAvailableSplitterTypes()
        ]);
    }
}