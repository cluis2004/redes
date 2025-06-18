<?php

namespace Tests\Unit;

use App\Services\OpticalLossService;
use App\DTOs\OpticalLossCalculationDto;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitarios para OpticalLossService
 * 
 * Valida que los cálculos de pérdida óptica sean precisos y consistentes
 */
class OpticalLossServiceTest extends TestCase
{
    private OpticalLossService $optical_loss_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->optical_loss_service = new OpticalLossService();
    }

    /**
     * Test cálculo básico con un splitter simple
     */
    public function test_basic_splitter_calculation()
    {
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => 3.0,
            'splitters' => [
                [
                    'id' => 'test_1',
                    'type' => '1:8',
                    'count' => 1,
                    'custom_loss' => null,
                    'use_custom' => false
                ]
            ],
            'fusion_count' => 0,
            'coupler_count' => 0,
            'fiber_distance_km' => 0,
            'design_margin_db' => 0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        $this->assertEquals(11.2, $result->splitter_loss_total);
        $this->assertEquals(11.2, $result->total_loss_db);
        $this->assertEquals(-8.2, $result->output_power_dbm);
    }

    /**
     * Test cálculo con múltiples splitters
     */
    public function test_multiple_splitters_calculation()
    {
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => 5.0,
            'splitters' => [
                [
                    'id' => 'test_1',
                    'type' => '1:2',
                    'count' => 1,
                    'custom_loss' => null,
                    'use_custom' => false
                ],
                [
                    'id' => 'test_2',
                    'type' => '1:4',
                    'count' => 2,
                    'custom_loss' => null,
                    'use_custom' => false
                ]
            ],
            'fusion_count' => 0,
            'coupler_count' => 0,
            'fiber_distance_km' => 0,
            'design_margin_db' => 0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        // 1:2 = 3.2 dB + 2x(1:4) = 2*7.2 = 14.4 dB
        // Total = 3.2 + 14.4 = 17.6 dB
        $this->assertEquals(17.6, $result->splitter_loss_total);
        $this->assertEquals(17.6, $result->total_loss_db);
        $this->assertEqualsWithDelta(-12.6, $result->output_power_dbm, 0.001);
    }

    /**
     * Test cálculo con pérdidas personalizadas
     */
    public function test_custom_loss_calculation()
    {
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => 4.0,
            'splitters' => [
                [
                    'id' => 'test_1',
                    'type' => '1:8',
                    'count' => 1,
                    'custom_loss' => 12.5,
                    'use_custom' => true
                ]
            ],
            'fusion_count' => 0,
            'coupler_count' => 0,
            'fiber_distance_km' => 0,
            'design_margin_db' => 0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        $this->assertEquals(12.5, $result->splitter_loss_total);
        $this->assertEquals(12.5, $result->total_loss_db);
        $this->assertEquals(-8.5, $result->output_power_dbm);
    }

    /**
     * Test cálculo completo con todos los componentes
     */
    public function test_complete_calculation()
    {
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => 3.0,
            'splitters' => [
                [
                    'id' => 'test_1',
                    'type' => '1:8',
                    'count' => 1,
                    'custom_loss' => null,
                    'use_custom' => false
                ]
            ],
            'fusion_count' => 4,
            'fusion_loss_per_unit' => 0.03,
            'coupler_count' => 2,
            'coupler_loss_per_unit' => 0.5,
            'fiber_distance_km' => 2.5,
            'fiber_loss_per_km' => 0.2,
            'design_margin_db' => 5.0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        // Splitter: 11.2 dB
        // Fusiones: 4 * 0.03 = 0.12 dB
        // Acopladores: 2 * 0.5 = 1.0 dB
        // Fibra: 2.5 * 0.2 = 0.5 dB
        // Margen: 5.0 dB
        // Total: 17.82 dB
        $this->assertEquals(11.2, $result->splitter_loss_total);
        $this->assertEqualsWithDelta(0.12, $result->fusion_loss_total, 0.001);
        $this->assertEquals(1.0, $result->coupler_loss_total);
        $this->assertEquals(0.5, $result->fiber_loss_total);
        $this->assertEquals(5.0, $result->design_margin);
        $this->assertEqualsWithDelta(17.82, $result->total_loss_db, 0.001);
        $this->assertEqualsWithDelta(-14.82, $result->output_power_dbm, 0.001);
    }

    /**
     * Test validación de potencia de salida
     */
    public function test_output_power_validation()
    {
        // Test potencia muy baja
        $warnings = $this->optical_loss_service->validateOutputPower(-30);
        $this->assertNotEmpty($warnings);
        $this->assertStringContainsString('muy baja', $warnings[0]);

        // Test potencia muy alta
        $warnings = $this->optical_loss_service->validateOutputPower(-5);
        $this->assertNotEmpty($warnings);
        $this->assertStringContainsString('muy alta', $warnings[0]);

        // Test potencia normal
        $warnings = $this->optical_loss_service->validateOutputPower(-15);
        $this->assertEmpty($warnings);

        // Test potencia en el límite
        $warnings = $this->optical_loss_service->validateOutputPower(-26);
        $this->assertNotEmpty($warnings);
        $this->assertStringContainsString('límite inferior', $warnings[0]);
    }

    /**
     * Test tipos de splitter disponibles
     */
    public function test_available_splitter_types()
    {
        $splitter_types = $this->optical_loss_service->getAvailableSplitterTypes();
        
        $this->assertIsArray($splitter_types);
        $this->assertArrayHasKey('1:2', $splitter_types);
        $this->assertArrayHasKey('1:4', $splitter_types);
        $this->assertArrayHasKey('1:8', $splitter_types);
        $this->assertArrayHasKey('1:16', $splitter_types);
        
        // Verificar valores específicos
        $this->assertEquals(3.2, $splitter_types['1:2']);
        $this->assertEquals(7.2, $splitter_types['1:4']);
        $this->assertEquals(11.2, $splitter_types['1:8']);
        $this->assertEquals(14.0, $splitter_types['1:16']);
    }

    /**
     * Test cálculo con valores extremos
     */
    public function test_edge_cases()
    {
        // Test con valores en cero
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => 0,
            'splitters' => [],
            'fusion_count' => 0,
            'coupler_count' => 0,
            'fiber_distance_km' => 0,
            'design_margin_db' => 0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        $this->assertEquals(0, $result->total_loss_db);
        $this->assertEquals(0, $result->output_power_dbm);

        // Test con valores negativos
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => -2.0,
            'splitters' => [],
            'fusion_count' => 0,
            'coupler_count' => 0,
            'fiber_distance_km' => 0,
            'design_margin_db' => 1.0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        $this->assertEquals(1.0, $result->total_loss_db);
        $this->assertEquals(-3.0, $result->output_power_dbm);
    }

    /**
     * Test estructura del desglose detallado
     */
    public function test_breakdown_structure()
    {
        $calculation_data = OpticalLossCalculationDto::fromArray([
            'input_power_dbm' => 3.0,
            'splitters' => [
                [
                    'id' => 'test_1',
                    'type' => '1:4',
                    'count' => 1,
                    'custom_loss' => null,
                    'use_custom' => false
                ]
            ],
            'fusion_count' => 2,
            'fusion_loss_per_unit' => 0.03,
            'coupler_count' => 1,
            'coupler_loss_per_unit' => 0.5,
            'fiber_distance_km' => 1.0,
            'fiber_loss_per_km' => 0.2,
            'design_margin_db' => 3.0
        ]);

        $result = $this->optical_loss_service->calculateTotalLoss($calculation_data);

        $this->assertIsArray($result->breakdown);
        $this->assertNotEmpty($result->breakdown);

        // Verificar que contiene todos los componentes esperados
        $component_types = array_column($result->breakdown, 'component');
        $this->assertContains('Splitter 1:4', $component_types);
        $this->assertContains('Fusiones', $component_types);
        $this->assertContains('Acopladores', $component_types);
        $this->assertContains('Fibra Óptica', $component_types);
        $this->assertContains('Margen de Diseño', $component_types);
    }
}