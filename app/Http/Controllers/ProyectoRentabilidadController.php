<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoRentabilidad;
use App\Models\ProyectoRentabilidadProyeccion;
use App\Models\ProyectoRentabilidadGasto;
use App\Models\ProyectoRentabilidadHoraExtra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoRentabilidadController extends Controller
{
    private function esAdminOEncargado()
    {
        $u = Auth::user();
        return $u && ($u->es_admin || $u->es_encargado);
    }

    public function index(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $query = Proyecto::with(['cliente', 'contacto']);

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $proyectos = $query->orderByDesc('created_at')->paginate(15);

        // Fetch rentabilidades mapped by project_id
        $rentabilidades = ProyectoRentabilidad::pluck('id', 'proyecto_id')->toArray();

        return view('administracion.rentabilidad.index', compact('proyectos', 'rentabilidades'));
    }

    public function show($proyectoId)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $proyecto = Proyecto::with(['cliente', 'lead.calculations'])->findOrFail($proyectoId);
        
        // Find or create the rentabilidad record for this project
        $rentabilidad = ProyectoRentabilidad::firstOrCreate(
            ['proyecto_id' => $proyectoId],
            [
                'fecha_analisis' => now(),
                'comision_porcentaje' => 10.00,
            ]
        );

        $proyecciones = ProyectoRentabilidadProyeccion::where('proyecto_rentabilidad_id', $rentabilidad->id)->get();
        $gastos = ProyectoRentabilidadGasto::where('proyecto_rentabilidad_id', $rentabilidad->id)->get();
        $horasExtras = ProyectoRentabilidadHoraExtra::where('proyecto_rentabilidad_id', $rentabilidad->id)->get();
        
        $comerciales = User::orderBy('name')->get();
        $proveedores = \App\Models\Proveedor::orderBy('nombre')->get();

        return view('administracion.rentabilidad.show', compact(
            'proyecto',
            'rentabilidad',
            'proyecciones',
            'gastos',
            'horasExtras',
            'comerciales',
            'proveedores'
        ));
    }

    public function comisionesLeads(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $leads = \App\Models\Lead::where('status', 'Ganado')
            ->with(['calculations', 'user'])
            ->get();

        $comisionesPorMes = [];

        foreach ($leads as $lead) {
            $equiposDOP = 0;
            
            // Loop calculations
            foreach ($lead->calculations as $calc) {
                $c = $calc->calculo_data;
                if (is_string($c)) {
                    $c = json_decode($c, true);
                }
                if ($c) {
                    $items = $c['items'] ?? [];
                    if (empty($items) && isset($c['costo'])) {
                        $items = [$c];
                    }
                    
                    $tasa = floatval($c['global_tasa'] ?? 63.23);
                    
                    foreach ($items as $item) {
                        $is_honorario = !empty($item['is_honorario']);
                        if (!$is_honorario) {
                            $costo = floatval($item['costo'] ?? 0);
                            $moneda = $item['moneda'] ?? 'DOP';
                            $qty = floatval($item['qty'] ?? 1);
                            $costoDOP = $moneda === 'USD' ? $costo * $tasa : $costo;
                            
                            $margin_perc = floatval($item['margin_perc'] ?? 0);
                            $gan_u = $costoDOP * ($margin_perc / 100);
                            $p_si = $costoDOP + $gan_u;
                            $price_si = floatval($item['adj_price'] ?? 0) > 0 ? floatval($item['adj_price']) : $p_si;
                            
                            $equiposDOP += $price_si * $qty;
                        }
                    }
                }
            }
            
            // Si no hay cálculos pero hay total_estimado, asumimos todo como equipos
            if ($equiposDOP == 0 && $lead->total_estimado > 0) {
                $equiposDOP = floatval($lead->total_estimado);
            }

            if ($equiposDOP > 0) {
                // Monto de comision (5% del total de equipos)
                $comision = $equiposDOP * 0.05;
                
                // Fecha de ganado (usando updated_at o created_at)
                $fecha = $lead->updated_at ?: now();
                $mesKey = $fecha->format('Y-m'); // Ej: 2026-06
                $mesNombre = $fecha->translatedFormat('F Y'); // Ej: junio 2026

                if (!isset($comisionesPorMes[$mesKey])) {
                    $comisionesPorMes[$mesKey] = [
                        'mes_nombre' => ucfirst($mesNombre),
                        'leads' => [],
                        'total_equipos' => 0,
                        'total_comision' => 0,
                    ];
                }

                $comisionesPorMes[$mesKey]['leads'][] = [
                    'lead' => $lead,
                    'monto_equipos' => $equiposDOP,
                    'comision' => $comision,
                    'fecha_ganado' => $fecha->format('d/m/Y'),
                    'comercial' => $lead->user ? $lead->user->name : 'Sin comercial',
                ];

                $comisionesPorMes[$mesKey]['total_equipos'] += $equiposDOP;
                $comisionesPorMes[$mesKey]['total_comision'] += $comision;
            }
        }

        // Ordenar los meses de forma descendente (el más reciente primero)
        krsort($comisionesPorMes);

        return view('administracion.rentabilidad.comisiones_leads', compact('comisionesPorMes'));
    }

    public function updateComision(Request $request, $proyectoId)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
            'comision_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $rentabilidad = ProyectoRentabilidad::where('proyecto_id', $proyectoId)->firstOrFail();
        $rentabilidad->update([
            'comision_porcentaje' => $request->comision_porcentaje,
            'comision_user_id' => $request->comision_user_id,
        ]);

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Configuración de comisión actualizada correctamente.');
    }

    public function storeProyeccion(Request $request, $proyectoId)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'cotizacion_no' => 'nullable|string|max:50',
            'referencia' => 'required|string|max:255',
            'abono' => 'required|numeric|min:0',
            'equipos_materiales' => 'required|numeric|min:0',
            'honorarios' => 'required|numeric|min:0',
            'fecha_pago' => 'nullable|date',
        ]);

        $rentabilidad = ProyectoRentabilidad::where('proyecto_id', $proyectoId)->firstOrFail();

        // Calculos automaticos
        $equipos = $request->equipos_materiales;
        $honorarios = $request->honorarios;
        $itbis = ($equipos + $honorarios) * 0.18;
        $totalFacturado = $equipos + $honorarios + $itbis;
        $totalAdeudado = $totalFacturado - $request->abono;

        ProyectoRentabilidadProyeccion::create([
            'proyecto_rentabilidad_id' => $rentabilidad->id,
            'cotizacion_no' => $request->cotizacion_no,
            'referencia' => $request->referencia,
            'abono' => $request->abono,
            'equipos_materiales' => $equipos,
            'honorarios' => $honorarios,
            'itbis' => $itbis,
            'total_facturado' => $totalFacturado,
            'total_adeudado' => $totalAdeudado,
            'fecha_pago' => $request->fecha_pago,
        ]);

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Fila de proyección agregada.');
    }

    public function destroyProyeccion($id)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $proyeccion = ProyectoRentabilidadProyeccion::findOrFail($id);
        $proyectoId = $proyeccion->rentabilidad->proyecto_id;
        $proyeccion->delete();

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Fila de proyección eliminada.');
    }

    public function storeGasto(Request $request, $proyectoId)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'fecha' => 'required|date',
            'factura' => 'nullable|string|max:50',
            'cuenta' => 'nullable|string|max:50',
            'proveedor' => 'required|string|max:255',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'clasificacion' => 'required|string|in:Honorario a Terceros,Uso Interno,Viáticos,Transporte,Equipo',
        ]);

        $rentabilidad = ProyectoRentabilidad::where('proyecto_id', $proyectoId)->firstOrFail();

        ProyectoRentabilidadGasto::create([
            'proyecto_rentabilidad_id' => $rentabilidad->id,
            'fecha' => $request->fecha,
            'factura' => $request->factura,
            'cuenta' => $request->cuenta,
            'proveedor' => $request->proveedor,
            'concepto' => $request->concepto,
            'monto' => $request->monto,
            'clasificacion' => $request->clasificacion,
        ]);

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Gasto agregado.');
    }

    public function destroyGasto($id)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $gasto = ProyectoRentabilidadGasto::findOrFail($id);
        $proyectoId = $gasto->rentabilidad->proyecto_id;
        $gasto->delete();

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Gasto eliminado.');
    }

    public function storeHoraExtra(Request $request, $proyectoId)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'fecha' => 'required|date',
            'colaborador' => 'required|string|max:255',
            'salario_mensual' => 'required|numeric|min:0',
            'al_100' => 'required|numeric|min:0',
            'cantidad_horas' => 'required|numeric|min:0',
        ]);

        $rentabilidad = ProyectoRentabilidad::where('proyecto_id', $proyectoId)->firstOrFail();

        // Formula Excel:
        // Salario Diario = Salario Mensual / 23.83
        // Salario por Hora = Salario Diario / 8
        // Total (hourly rate) = Salario por Hora * (1 + (al_100 / 100))  <- double pay if al_100 is 100
        // Total a pagar = Total * cantidad_horas
        
        $salarioMensual = $request->salario_mensual;
        $salarioDiario = $salarioMensual / 23.83;
        $salarioPorHora = $salarioDiario / 8;
        $totalHora = $salarioPorHora * (1 + ($request->al_100 / 100));
        $totalPagar = $totalHora * $request->cantidad_horas;

        ProyectoRentabilidadHoraExtra::create([
            'proyecto_rentabilidad_id' => $rentabilidad->id,
            'fecha' => $request->fecha,
            'colaborador' => $request->colaborador,
            'salario_mensual' => $salarioMensual,
            'salario_diario' => $salarioDiario,
            'salario_por_hora' => $salarioPorHora,
            'al_100' => $request->al_100,
            'total' => $totalHora,
            'cantidad_horas' => $request->cantidad_horas,
            'total_pagar' => $totalPagar,
        ]);

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Registro de horas extras agregado.');
    }

    public function destroyHoraExtra($id)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $he = ProyectoRentabilidadHoraExtra::findOrFail($id);
        $proyectoId = $he->rentabilidad->proyecto_id;
        $he->delete();

        return redirect()->route('administracion.rentabilidad.show', $proyectoId)
            ->with('success', 'Registro de horas extras eliminado.');
    }
}
