<?php

namespace App\Http\Controllers;

use App\Models\SecurityCompany;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SecurityCompanyController extends Controller
{
    public function index()
    {
        return view('security-companies.index', [
            'companies' => SecurityCompany::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255', 'unique:security_companies,name'],
            'contact'         => ['required', 'string', 'max:255'],
            'contract_detail' => ['required', 'string', 'max:255'],
        ]);

        $data['status'] = 'active';

        SecurityCompany::create($data);

        return redirect()->route('security-companies.index')->with('status', 'Security company added.');
    }

    public function show(SecurityCompany $securityCompany)
    {
        abort(404);
    }

    public function edit(SecurityCompany $securityCompany)
    {
        abort(404);
    }

    public function update(Request $request, SecurityCompany $securityCompany)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255', Rule::unique('security_companies', 'name')->ignore($securityCompany)],
            'contact'         => ['required', 'string', 'max:255'],
            'contract_detail' => ['required', 'string', 'max:255'],
            'status'          => ['required', Rule::in(SecurityCompany::STATUSES)],
        ]);

        $securityCompany->update($data);

        return redirect()->route('security-companies.index')->with('status', 'Security company updated.');
    }

    public function destroy(SecurityCompany $securityCompany)
    {
        try {
            $securityCompany->delete();
        } catch (QueryException $e) {
            return redirect()->route('security-companies.index')
                ->withErrors(['delete' => 'Cannot delete: this company is used in a deployment.']);
        }

        return redirect()->route('security-companies.index')->with('status', 'Security company deleted.');
    }
}
