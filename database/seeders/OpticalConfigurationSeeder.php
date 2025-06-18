<?php

namespace Database\Seeders;

use App\Models\OpticalConfiguration;
use Illuminate\Database\Seeder;

/**
 * Seeder para crear configuraciones predefinidas de cálculo óptico
 * 
 * Crea plantillas comunes para diferentes escenarios de redes FTTH
 */
class OpticalConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'name' => 'Red Residencial Básica',
                'description' => 'Configuración típica para red FTTH residencial de corta distancia',
                'parameters' => [
                    'input_power_dbm' => 3.0,
                    'splitters' => [
                        [
                            'id' => 'default_1',
                            'type' => '1:8',
                            'count' => 1,
                            'custom_loss' => null,
                            'use_custom' => false
                        ]
                    ],
                    'fusion_count' => 2,
                    'fusion_loss_per_unit' => 0.03,
                    'coupler_count' => 4,
                    'coupler_loss_per_unit' => 0.5,
                    'fiber_distance_km' => 1.5,
                    'fiber_loss_per_km' => 0.2,
                    'design_margin_db' => 3.0
                ],
                'total_loss_db' => 18.5,
                'output_power_dbm' => -15.5,
                'is_template' => true
            ],
            [
                'name' => 'Red Empresarial Media Distancia',
                'description' => 'Configuración para conexiones empresariales de mediana distancia',
                'parameters' => [
                    'input_power_dbm' => 5.0,
                    'splitters' => [
                        [
                            'id' => 'default_1',
                            'type' => '1:4',
                            'count' => 1,
                            'custom_loss' => null,
                            'use_custom' => false
                        ],
                        [
                            'id' => 'default_2',
                            'type' => '1:8',
                            'count' => 1,
                            'custom_loss' => null,
                            'use_custom' => false
                        ]
                    ],
                    'fusion_count' => 6,
                    'fusion_loss_per_unit' => 0.03,
                    'coupler_count' => 2,
                    'coupler_loss_per_unit' => 0.5,
                    'fiber_distance_km' => 8.0,
                    'fiber_loss_per_km' => 0.2,
                    'design_margin_db' => 5.0
                ],
                'total_loss_db' => 26.98,
                'output_power_dbm' => -21.98,
                'is_template' => true
            ],
            [
                'name' => 'Red de Larga Distancia',
                'description' => 'Configuración para enlaces de larga distancia con múltiples divisores',
                'parameters' => [
                    'input_power_dbm' => 8.0,
                    'splitters' => [
                        [
                            'id' => 'default_1',
                            'type' => '1:2',
                            'count' => 1,
                            'custom_loss' => null,
                            'use_custom' => false
                        ],
                        [
                            'id' => 'default_2',
                            'type' => '1:16',
                            'count' => 1,
                            'custom_loss' => null,
                            'use_custom' => false
                        ]
                    ],
                    'fusion_count' => 8,
                    'fusion_loss_per_unit' => 0.03,
                    'coupler_count' => 6,
                    'coupler_loss_per_unit' => 0.5,
                    'fiber_distance_km' => 15.0,
                    'fiber_loss_per_km' => 0.2,
                    'design_margin_db' => 6.0
                ],
                'total_loss_db' => 29.44,
                'output_power_dbm' => -21.44,
                'is_template' => true
            ],
            [
                'name' => 'Red de Alta Densidad',
                'description' => 'Configuración para áreas de alta densidad con múltiples niveles de división',
                'parameters' => [
                    'input_power_dbm' => 4.0,
                    'splitters' => [
                        [
                            'id' => 'default_1',
                            'type' => '1:4',
                            'count' => 2,
                            'custom_loss' => null,
                            'use_custom' => false
                        ],
                        [
                            'id' => 'default_2',
                            'type' => '1:8',
                            'count' => 1,
                            'custom_loss' => null,
                            'use_custom' => false
                        ]
                    ],
                    'fusion_count' => 4,
                    'fusion_loss_per_unit' => 0.03,
                    'coupler_count' => 8,
                    'coupler_loss_per_unit' => 0.5,
                    'fiber_distance_km' => 3.0,
                    'fiber_loss_per_km' => 0.2,
                    'design_margin_db' => 4.0
                ],
                'total_loss_db' => 39.72,
                'output_power_dbm' => -35.72,
                'is_template' => true
            ]
        ];

        foreach ($configurations as $config) {
            OpticalConfiguration::create($config);
        }
    }
}