<?php

namespace App\Http\Controllers;

use App\Models\Severity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeverityController extends Controller
{
    public function index()
    {
        $severities = Severity::orderBy('id')->get();

        return view('settings.severity-levels', ['severities' => $severities]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255', 'unique:severities,name'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        Severity::create($data);

        return redirect()->route('settings.severity-levels.index')
            ->with('status', 'Severity level added.');
    }

    public function update(Request $request, Severity $severity)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255', Rule::unique('severities', 'name')->ignore($severity->id)],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $severity->update($data);

        return redirect()->route('settings.severity-levels.index')
            ->with('status', 'Severity level updated.');
    }

    public function destroy(Severity $severity)
    {
        $severity->delete();

        return redirect()->route('settings.severity-levels.index')
            ->with('status', 'Severity level deleted.');
    }
}
