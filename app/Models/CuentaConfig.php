<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cuentas que usa el sistema para los asientos automáticos.
 * Si la empresa no configuró una clave, se busca la cuenta del plan modelo por código.
 */
class CuentaConfig extends Model
{
    use BelongsToEmpresa;

    protected $table = 'cuentas_config';

    protected $fillable = ['empresa_id', 'clave', 'cuenta_id'];

    /** clave => [grupo, descripción, código por defecto en el plan modelo] */
    public const CLAVES = [
        'deudores_ventas'         => ['Ventas',  'Deudores por ventas (cuenta del cliente por defecto)', '1.1.03.001'],
        'ventas'                  => ['Ventas',  'Ventas (imputación por defecto)',                     '4.1.01.001'],
        'iva_debito'              => ['Ventas',  'IVA Débito Fiscal',                                   '2.1.04.001'],
        'perc_iva_practicadas'    => ['Ventas',  'Percepciones de IVA practicadas (a depositar)',       '2.1.04.004'],
        'perc_iibb_practicadas'   => ['Ventas',  'Percepciones de IIBB practicadas (a depositar)',      '2.1.04.009'],
        'otros_tributos_ventas'   => ['Ventas',  'Otros tributos facturados',                           '2.1.05.001'],
        'proveedores'             => ['Compras', 'Proveedores (cuenta del proveedor por defecto)',      '2.1.01.001'],
        'compras'                 => ['Compras', 'Compras / gastos (imputación por defecto)',           '5.3.01.012'],
        'iva_credito'             => ['Compras', 'IVA Crédito Fiscal',                                  '1.1.04.001'],
        'perc_iva_sufridas'       => ['Compras', 'Percepciones de IVA sufridas',                        '1.1.04.004'],
        'perc_iibb_sufridas'      => ['Compras', 'Percepciones de IIBB sufridas',                       '1.1.04.008'],
        'otros_tributos_compras'  => ['Compras', 'Otros tributos (impuestos internos, tasas)',          '5.3.01.012'],
        'iva_saldo_tecnico'       => ['Liquidación de IVA', 'IVA saldo a favor técnico',               '1.1.04.002'],
        'iva_saldo_libre'         => ['Liquidación de IVA', 'IVA saldo a favor de libre disponibilidad', '1.1.04.003'],
        'ret_iva_sufridas'        => ['Liquidación de IVA', 'Retenciones de IVA sufridas',             '1.1.04.005'],
        'iva_a_pagar'             => ['Liquidación de IVA', 'IVA a pagar',                             '2.1.04.002'],
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    /** Devuelve la cuenta configurada para la clave (o la del plan modelo), o null si no existe. */
    public static function cuentaPara(string $clave): ?Cuenta
    {
        $config = static::where('clave', $clave)->first();
        if ($config?->cuenta_id && $cuenta = Cuenta::find($config->cuenta_id)) {
            return $cuenta;
        }

        $codigo = self::CLAVES[$clave][2] ?? null;
        return $codigo ? Cuenta::where('codigo', $codigo)->where('imputable', true)->first() : null;
    }

    public static function idPara(string $clave): ?int
    {
        return static::cuentaPara($clave)?->id;
    }
}
