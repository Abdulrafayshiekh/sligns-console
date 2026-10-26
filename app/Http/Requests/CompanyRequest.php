<?php

namespace App\Http\Requests;

use App\Support\Options;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($list) => array_values(array_filter(
            array_map(fn ($value) => trim((string) $value), (array) $list),
            fn ($value) => $value !== ''
        ));

        $this->merge([
            'addresses' => $clean($this->input('addresses', [])),
            'phones' => $clean($this->input('phones', [])),
            'emails' => $clean($this->input('emails', [])),
            'text_opt_in' => $this->boolean('text_opt_in'),
            'interface_color' => strtolower(trim((string) $this->input('interface_color'))),
        ]);
    }

    public function rules(): array
    {
        $company = $this->route('company');
        $admin = $company?->admins()->first();
        $languages = array_keys(Options::languages());

        return [
            'console_language' => ['required', Rule::in($languages)],
            'interface_color' => ['required', 'regex:/^#[0-9a-f]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024', 'dimensions:max_width=250,max_height=120'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'preferred_language' => ['required', Rule::in($languages)],
            'about' => ['nullable', 'string', 'max:2000'],
            'addresses' => ['array', 'max:10'],
            'addresses.*' => ['string', 'max:255'],
            'phones' => ['array', 'max:10'],
            'phones.*' => ['string', 'max:40'],
            'emails' => ['array', 'max:10'],
            'emails.*' => ['email', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', Rule::in(Options::countries())],
            'time_zone' => ['required', 'timezone:all'],
            'text_opt_in' => ['boolean'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin?->id)],
            'admin_password' => [$company ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    public function attributes(): array
    {
        return [
            'interface_color' => 'interface colour',
            'name' => 'company name',
            'addresses.*' => 'address',
            'phones.*' => 'phone',
            'emails.*' => 'email',
            'admin_name' => 'console administrator name',
            'admin_email' => 'console sign-in email',
            'admin_password' => 'console password',
        ];
    }

    public function messages(): array
    {
        return [
            'interface_color.regex' => 'The interface colour must be a hex colour such as #c90076.',
            'logo.dimensions' => 'The logo can be at most 250 × 120 pixels.',
        ];
    }

    public function companyData(): array
    {
        return $this->safe()->only([
            'console_language',
            'interface_color',
            'name',
            'contact_person',
            'preferred_language',
            'about',
            'addresses',
            'phones',
            'emails',
            'city',
            'country',
            'time_zone',
            'text_opt_in',
        ]);
    }
}
