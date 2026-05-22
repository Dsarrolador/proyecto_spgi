<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoCuenta;
use App\Models\ClienteMaestro;
use Carbon\Carbon;

class EstadoCuentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Try to fetch some master clients or find them by common names
        $clientes = [
            'APR' => ClienteMaestro::where('nombre', 'like', '%APR%')->first(),
            'Ocean Brill' => ClienteMaestro::where('nombre', 'like', '%Ocean%')->first(),
            'Caleb Brett' => ClienteMaestro::where('nombre', 'like', '%Caleb%')->first(),
            'Saybolt' => ClienteMaestro::where('nombre', 'like', '%Saybolt%')->first(),
        ];

        $today = Carbon::today();

        $facturas = [
            // APR DOP Invoices
            [
                'cliente_nombre' => 'APR',
                'cliente_maestro_id' => $clientes['APR']->id ?? null,
                'factura_no' => 'FCT-001',
                'nfc' => 'B0100001201',
                'fecha' => $today->copy()->subDays(45),
                'fecha_vencimiento' => $today->copy()->subDays(15),
                'producto' => 'IGUALA SOPORTE MENSUAL ABRIL',
                'balance' => 45000.00,
                'moneda' => 'DOP',
                'tasa_cambio' => null,
                'fecha_pago' => $today->copy()->subDays(12),
                'fecha_aplicado' => $today->copy()->subDays(12),
                'recibo_no' => 'RC-501',
                'total_pagado' => 45000.00,
            ],
            [
                'cliente_nombre' => 'APR',
                'cliente_maestro_id' => $clientes['APR']->id ?? null,
                'factura_no' => 'FCT-002',
                'nfc' => 'B0100001202',
                'fecha' => $today->copy()->subDays(15),
                'fecha_vencimiento' => $today->copy()->addDays(15),
                'producto' => 'IGUALA SOPORTE MENSUAL MAYO',
                'balance' => 45000.00,
                'moneda' => 'DOP',
                'tasa_cambio' => null,
                'fecha_pago' => null,
                'fecha_aplicado' => null,
                'recibo_no' => null,
                'total_pagado' => null,
            ],
            // APR USD Invoices
            [
                'cliente_nombre' => 'APR',
                'cliente_maestro_id' => $clientes['APR']->id ?? null,
                'factura_no' => 'FCT-003',
                'nfc' => 'B0100001203',
                'fecha' => $today->copy()->subDays(40),
                'fecha_vencimiento' => $today->copy()->subDays(10),
                'producto' => 'DESARROLLO DE SOFTWARE FASE 1',
                'balance' => 1200.00,
                'moneda' => 'USD',
                'tasa_cambio' => 60.10,
                'fecha_pago' => null,
                'fecha_aplicado' => null,
                'recibo_no' => null,
                'total_pagado' => null,
            ],
            
            // Ocean Brill DOP Invoices
            [
                'cliente_nombre' => 'Ocean Brill',
                'cliente_maestro_id' => $clientes['Ocean Brill']->id ?? null,
                'factura_no' => 'FCT-004',
                'nfc' => 'B0100001204',
                'fecha' => $today->copy()->subDays(50),
                'fecha_vencimiento' => $today->copy()->subDays(20),
                'producto' => 'MANTENIMIENTO INDUSTRIAL REDES',
                'balance' => 75000.00,
                'moneda' => 'DOP',
                'tasa_cambio' => null,
                'fecha_pago' => $today->copy()->subDays(18),
                'fecha_aplicado' => $today->copy()->subDays(18),
                'recibo_no' => 'RC-502',
                'total_pagado' => 75000.00,
            ],
            [
                'cliente_nombre' => 'Ocean Brill',
                'cliente_maestro_id' => $clientes['Ocean Brill']->id ?? null,
                'factura_no' => 'FCT-005',
                'nfc' => 'B0100001205',
                'fecha' => $today->copy()->subDays(35),
                'fecha_vencimiento' => $today->copy()->subDays(5),
                'producto' => 'REPARACIÓN SERVIDOR DE DATOS',
                'balance' => 32000.00,
                'moneda' => 'DOP',
                'tasa_cambio' => null,
                'fecha_pago' => null,
                'fecha_aplicado' => null,
                'recibo_no' => null,
                'total_pagado' => null,
            ],

            // Caleb Brett USD Invoices
            [
                'cliente_nombre' => 'Caleb Brett',
                'cliente_maestro_id' => $clientes['Caleb Brett']->id ?? null,
                'factura_no' => 'FCT-006',
                'nfc' => 'B0100001206',
                'fecha' => $today->copy()->subDays(10),
                'fecha_vencimiento' => $today->copy()->addDays(20),
                'producto' => 'LICENCIAS MENSUALES CLOUD',
                'balance' => 350.00,
                'moneda' => 'USD',
                'tasa_cambio' => 60.15,
                'fecha_pago' => null,
                'fecha_aplicado' => null,
                'recibo_no' => null,
                'total_pagado' => null,
            ],

            // Saybolt DOP Invoices
            [
                'cliente_nombre' => 'Saybolt',
                'cliente_maestro_id' => $clientes['Saybolt']->id ?? null,
                'factura_no' => 'FCT-007',
                'nfc' => 'B0100001207',
                'fecha' => $today->copy()->subDays(5),
                'fecha_vencimiento' => $today->copy()->addDays(25),
                'producto' => 'SOPORTE TÉCNICO PRESENCIAL',
                'balance' => 15000.00,
                'moneda' => 'DOP',
                'tasa_cambio' => null,
                'fecha_pago' => null,
                'fecha_aplicado' => null,
                'recibo_no' => null,
                'total_pagado' => null,
            ]
        ];

        foreach ($facturas as $f) {
            EstadoCuenta::create($f);
        }
    }
}
