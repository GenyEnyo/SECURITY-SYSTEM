<?php

namespace App\Http\Controllers;

use App\Models\SecurityCompany;
use App\Models\SecurityCompanyStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SecurityCompanyStatusController extends Controller
{
    public function index()
    {
        $statuses = SecurityCompanyStatus::orderBy('id')->get();

        return view('settings.company-statuses', ['statuses' => $statuses]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:security_company_statuses,name'],
        ]);

        SecurityCompanyStatus::create($data);

        return redirect()->route('settings.company-statuses.index')
            ->with('status', 'Company status added.');
    }

    public function update(Request $request, SecurityCompanyStatus $companyStatus)
    {
        if ($companyStatus->isProtected()) {
            return redirect()->route('settings.company-statuses.index')
                ->with('error', "\"{$companyStatus->name}\" is used by the system and cannot be changed.");
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('security_company_statuses', 'name')->ignore($companyStatus->id)],
        ]);

        DB::transaction(function () use ($companyStatus, $data) {
            // Companies store the status by name, so carry the rename over to them.
            SecurityCompany::withoutGlobalScopes()
                ->where('status', $companyStatus->name)
                ->update(['status' => $data['name']]);

            $companyStatus->update($data);
        });

        return redirect()->route('settings.company-statuses.index')
            ->with('status', 'Company status updated.');
    }

    public function destroy(SecurityCompanyStatus $companyStatus)
    {
        if ($companyStatus->isProtected()) {
            return redirect()->route('settings.company-statuses.index')
                ->with('error', "\"{$companyStatus->name}\" is used by the system and cannot be deleted.");
        }

        if (SecurityCompany::withoutGlobalScopes()->where('status', $companyStatus->name)->exists()) {
            return redirect()->route('settings.company-statuses.index')
                ->with('error', "\"{$companyStatus->name}\" is in use by a security company and cannot be deleted.");
        }

        $companyStatus->delete();

        return redirect()->route('settings.company-statuses.index')
            ->with('status', 'Company status deleted.');
    }
}
