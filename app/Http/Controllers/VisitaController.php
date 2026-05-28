<?php

namespace App\Http\Controllers;

use App\Models\ChecklistTemplate;
use App\Models\Visita;
use App\Models\VisitaRespuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class VisitaController extends Controller
{
    public function index()
    {
        $visitas = Visita::with('template', 'user')->orderBy('created_at', 'desc')->get();
        return view('visitas.index', compact('visitas'));
    }

    public function create()
    {
        $templates = ChecklistTemplate::all();
        return view('visitas.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_visitado' => 'required|string|max:150',
            'correo_visitado' => 'nullable|email|max:150',
            'nombre_recibio' => 'nullable|string|max:150',
            'telefono_recibio' => 'nullable|string|max:150',
            'template_id' => 'required|exists:checklist_templates,id',
        ]);

        $visita = Visita::create([
            'nombre_visitado' => $request->nombre_visitado,
            'correo_visitado' => $request->correo_visitado,
            'nombre_recibio' => $request->nombre_recibio,
            'telefono_recibio' => $request->telefono_recibio,
            'template_id' => $request->template_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('visitas.edit', $visita->id)
            ->with('success', 'Visita registrada. Complete el cuestionario a continuación.');
    }

    public function edit($id)
    {
        $visita = Visita::with('template.questions.predefinedAnswers', 'respuestas')->findOrFail($id);
        return view('visitas.edit', compact('visita'));
    }

    public function update(Request $request, $id)
    {
        $visita = Visita::findOrFail($id);

        $data = $request->except(['_token', '_method']);
        $respuestas = $data['respuestas'] ?? [];
        
        $totalPuntos = 0;

        DB::transaction(function () use ($visita, $respuestas, &$totalPuntos, $data) {
            foreach ($respuestas as $question_id => $ans) {
                $puntos = intval($ans['puntos'] ?? 0);
                $totalPuntos += $puntos;
                
                $visita->respuestas()->updateOrCreate(
                    ['question_id' => $question_id],
                    [
                        'respuesta_seleccionada' => $ans['respuesta'] ?? null,
                        'puntos' => $puntos,
                        'observaciones' => $ans['observaciones'] ?? null,
                        'recomendacion' => $ans['recomendacion'] ?? null,
                    ]
                );
            }

            // Cálculo del estado
            $estado_cliente = 'Óptimo';
            if ($totalPuntos < 30) {
                $estado_cliente = 'Crítico';
            } elseif ($totalPuntos < 45) {
                $estado_cliente = 'Regular';
            }

            $visita->update([
                'total_puntos' => $totalPuntos,
                'estado_cliente' => $estado_cliente,
                'accion_sugerida' => $data['accion_sugerida'] ?? null
            ]);
        });

        return redirect()->route('visitas.show', $visita->id)
            ->with('success', 'Cuestionario de visita guardado correctamente.');
    }

    public function show($id)
    {
        $visita = Visita::with('template.questions.predefinedAnswers', 'respuestas', 'user')->findOrFail($id);
        return view('visitas.show', compact('visita'));
    }

    public function destroy($id)
    {
        $visita = Visita::findOrFail($id);
        $visita->delete();

        return redirect()->route('visitas.index')
            ->with('success', 'Visita eliminada correctamente.');
    }

    public function generarPdf($id)
    {
        $visita = Visita::with('template.questions.predefinedAnswers', 'respuestas', 'user')->findOrFail($id);
        
        $pdf = Pdf::loadView('visitas.pdf', compact('visita'));
        return $pdf->stream('Reporte_Visita_' . $visita->id . '.pdf');
    }

    public function enviarCorreo(Request $request, $id)
    {
        $request->validate([
            'destinatario' => 'required|email',
        ]);

        $visita = Visita::with('template.questions.predefinedAnswers', 'respuestas', 'user')->findOrFail($id);

        try {
            $pdf = Pdf::loadView('visitas.pdf', compact('visita'));
            $pdfContent = $pdf->output();

            Mail::send('visitas.mail', ['visita' => $visita], function($message) use ($request, $pdfContent, $visita) {
                $message->to($request->destinatario)
                        ->subject('Reporte de Visita Técnica - ' . $visita->nombre_visitado)
                        ->attachData($pdfContent, 'Reporte_Visita_' . $visita->id . '.pdf', [
                            'mime' => 'application/pdf',
                        ]);
            });

            return redirect()->route('visitas.show', $visita->id)
                ->with('success', 'El reporte ha sido enviado por correo exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al enviar el correo (SMTP): ' . $e->getMessage()]);
        }
    }
}
