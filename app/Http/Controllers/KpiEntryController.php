<?php

namespace App\Http\Controllers;

use App\Models\KpiGroup;
use Illuminate\Http\Request;

class KpiEntryController extends Controller
{
    public function index()
    {
        $groups = KpiGroup::with('subItems')->orderBy('id')->get();

        return view('kpi.entry', ['groups' => $groups]);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $id)
    {
        abort(404);
    }
}
