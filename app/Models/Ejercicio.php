<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    use BelongsToEmpresa;

    protected $fillable = [
        'empresa_id', 'numero', 'descripcion', 'fecha_inicio', 'fecha_cierre',
        'estado', 'bloqueado_hasta',
    ];

    protected $casts = [
        'fecha_inicio'    => 'date',
        'fecha_cierre'    => 'date',
        'bloqueado_hasta' => 'date',
    ];

    public function estaAbierto(): bool
    {
        return $this->estado === 'abierto';
    }

    /** Indica si se puede registrar un asiento en la fecha dada. */
    public function admiteFecha(Carbon|string $fecha): bool
    {
        $fecha = Carbon::parse($fecha)->startOfDay();

        if (! $this->estaAbierto()) {
            return false;
        }
        if ($fecha->lt($this->fecha_inicio) || $fecha->gt($this->fecha_cierre)) {
            return false;
        }
        if ($this->bloqueado_hasta && $fecha->lte($this->bloqueado_hasta)) {
            return false;
        }
        return true;
    }

    public function getNombreAttribute(): string
    {
        return "Ejercicio {$this->numero} ({$this->fecha_inicio->format('d/m/Y')} - {$this->fecha_cierre->format('d/m/Y')})";
    }
}
