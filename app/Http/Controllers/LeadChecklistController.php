<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ChecklistTemplate;
use App\Models\LeadChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadChecklistController extends Controller
{
    public function create(Lead $lead)
    {
        $templates = ChecklistTemplate::all();
        return view('leads.checklists.create', compact('lead', 'templates'));
    }

    public function store(Request $request, Lead $lead)
    {
        $request->validate(['template_id' => 'required|exists:checklist_templates,id']);
        
        $template = ChecklistTemplate::with('questions.predefinedAnswers')->findOrFail($request->template_id);
        
        $checklist = LeadChecklist::create([
            'lead_id' => $lead->id,
            'template_id' => $template->id,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('leads.checklists.edit', [$lead->id, $checklist->id]);
    }

    public function edit(Lead $lead, LeadChecklist $checklist)
    {
        $checklist->load('template.questions.predefinedAnswers', 'answers');
        return view('leads.checklists.edit', compact('lead', 'checklist'));
    }

    public function update(Request $request, Lead $lead, LeadChecklist $checklist)
    {
        $data = $request->except(['_token', '_method']);
        $respuestas = $data['respuestas'] ?? [];
        
        $totalPuntos = 0;

        foreach ($respuestas as $question_id => $ans) {
            $puntos = $ans['puntos'] ?? 0;
            $totalPuntos += $puntos;
            
            $checklist->answers()->updateOrCreate(
                ['question_id' => $question_id],
                [
                    'respuesta_seleccionada' => $ans['respuesta'] ?? null,
                    'puntos' => $puntos,
                    'observaciones' => $ans['observaciones'] ?? null,
                    'recomendacion' => $ans['recomendacion'] ?? null,
                ]
            );
        }

        $estado_cliente = 'Óptimo';
        if ($totalPuntos < 30) {
            $estado_cliente = 'Crítico';
        } elseif ($totalPuntos < 45) {
            $estado_cliente = 'Regular';
        }

        $checklist->update([
            'total_puntos' => $totalPuntos,
            'estado_cliente' => $estado_cliente,
            'accion_sugerida' => $data['accion_sugerida'] ?? null
        ]);

        return redirect()->route('leads.show', $lead->id)->with('success', 'Checklist guardado correctamente.');
    }
}
