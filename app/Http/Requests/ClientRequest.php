<?php

namespace App\Http\Requests;

use App\Support\Options;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isCoach();
    }

    public function rules(): array
    {
        $client = $this->route('client');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($client?->id)],
            'birthday' => ['required', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:40'],
            'title' => ['nullable', Rule::in(Options::titles())],
            'marital_status' => ['nullable', Rule::in(Options::maritalStatuses())],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'industry' => ['nullable', Rule::in(Options::industries())],
            'country' => ['nullable', Rule::in(Options::countries())],
            'language' => ['nullable', Rule::in(array_keys(Options::languages()))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('full name'),
        ];
    }

    public function clientData(): array
    {
        return $this->safe()->except('notes');
    }
}
