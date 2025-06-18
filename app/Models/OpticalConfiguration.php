<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Modelo para configuraciones predefinidas de cálculo óptico
 * 
 * Permite guardar y cargar configuraciones de parámetros
 * para facilitar el trabajo con diseños recurrentes.
 */
class OpticalConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'parameters',
        'total_loss_db',
        'output_power_dbm',
        'is_template',
        'created_by'
    ];

    protected $casts = [
        'parameters' => 'array',
        'total_loss_db' => 'decimal:2',
        'output_power_dbm' => 'decimal:2',
        'is_template' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para obtener solo plantillas predefinidas
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope para obtener configuraciones de usuario específico
     */
    public function scopeByUser($query, $user_identifier)
    {
        return $query->where('created_by', $user_identifier);
    }

    /**
     * Accessor para formatear el nombre de forma amigable
     */
    protected function formattedName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => ucfirst($attributes['name'])
        );
    }

    /**
     * Accessor para obtener un resumen de la configuración
     */
    protected function summary(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $params = $attributes['parameters'] ? json_decode($attributes['parameters'], true) : [];
                
                $fiber_distance = $params['fiber_distance_km'] ?? 0;
                $splitter_count = count($params['splitters'] ?? []);
                
                return sprintf(
                    'Fibra: %s km, Splitters: %d, Pérdida: %s dB',
                    number_format($fiber_distance, 1),
                    $splitter_count,
                    number_format($attributes['total_loss_db'] ?? 0, 2)
                );
            }
        );
    }

    /**
     * Crear configuración desde array de parámetros
     */
    public static function createFromParameters(array $parameters, string $name, ?string $description = null, ?string $user_identifier = null): self
    {
        return self::create([
            'name' => $name,
            'description' => $description,
            'parameters' => $parameters,
            'created_by' => $user_identifier,
            'is_template' => false
        ]);
    }

    /**
     * Actualizar resultados calculados
     */
    public function updateResults(float $total_loss_db, float $output_power_dbm): void
    {
        $this->update([
            'total_loss_db' => $total_loss_db,
            'output_power_dbm' => $output_power_dbm
        ]);
    }
}