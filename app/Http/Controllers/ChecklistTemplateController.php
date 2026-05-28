<?php

namespace App\Http\Controllers;

use App\Models\ChecklistTemplate;
use App\Models\ChecklistQuestion;
use App\Models\ChecklistPredefinedAnswer;
use Illuminate\Http\Request;

class ChecklistTemplateController extends Controller
{
    public function index()
    {
        $templates = ChecklistTemplate::all();
        return view('checklists.index', compact('templates'));
    }

    public function create()
    {
        return view('checklists.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $template = ChecklistTemplate::create($request->all());
        return redirect()->route('checklists.edit', $template->id)->with('success', 'Plantilla creada. Ahora agrega las preguntas.');
    }

    public function edit(ChecklistTemplate $checklist)
    {
        $checklist->load('questions.predefinedAnswers');
        return view('checklists.edit', compact('checklist'));
    }

    public function update(Request $request, ChecklistTemplate $checklist)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $checklist->update($request->all());
        return redirect()->route('checklists.index')->with('success', 'Plantilla actualizada.');
    }

    public function destroy(ChecklistTemplate $checklist)
    {
        $checklist->delete();
        return redirect()->route('checklists.index')->with('success', 'Plantilla eliminada.');
    }

    // --- PREGUNTAS ---
    public function storeQuestion(Request $request, ChecklistTemplate $checklist)
    {
        $request->validate(['pregunta' => 'required|string']);
        $checklist->questions()->create($request->all());
        return redirect()->back()->with('success', 'Pregunta agregada.');
    }

    public function destroyQuestion(ChecklistQuestion $question)
    {
        $question->delete();
        return redirect()->back()->with('success', 'Pregunta eliminada.');
    }

    // --- RESPUESTAS PREDETERMINADAS ---
    public function storeAnswer(Request $request, ChecklistQuestion $question)
    {
        $request->validate([
            'respuesta' => 'required|string',
            'puntos' => 'required|integer'
        ]);
        $question->predefinedAnswers()->create($request->all());
        return redirect()->back()->with('success', 'Respuesta agregada.');
    }

    public function destroyAnswer(ChecklistPredefinedAnswer $answer)
    {
        $answer->delete();
        return redirect()->back()->with('success', 'Respuesta eliminada.');
    }
}
