<?php

namespace App\Http\Requests;

use App\Models\Building;
use App\Models\Location;
use App\Models\Place;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'location_id'        => ['required', Rule::in(Location::pluck('id'))],
            'building_id'        => ['required', Rule::in(Building::pluck('id'))],
            'place_id'           => ['required', Rule::in(Place::pluck('id'))],
            'severity_id'        => ['required', 'exists:severities,id'],
            'description'        => ['nullable', 'string', 'max:5000'],
            'attachment'         => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx', 'max:5120'],
        ];
    }
}
