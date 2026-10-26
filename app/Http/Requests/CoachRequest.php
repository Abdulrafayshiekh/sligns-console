<?php

namespace App\Http\Requests;

use App\Support\Options;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isCompany();
    }

    public function rules(): array
    {
        $coach = $this->route('coach');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($coach?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', Rule::in(Options::countries())],
            'password' => [$coach ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('full name'),
        ];
    }
}
