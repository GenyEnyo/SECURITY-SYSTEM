<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentOccurrenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'incident_type_id'   => ['required', 'exists:incident_types,id'],
            'occurred_at'        => ['required', 'date'],
            'location_id'        => ['required', 'exists:locations,id'],
            'severity_id'        => ['required', 'exists:severities,id'],
            'description'        => ['nullable', 'string', 'max:5000'],
            'attachment'         => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx', 'max:5120'],
        ];
    }
}
