<?php

namespace App\Http\Requests\Stats;

use Illuminate\Foundation\Http\FormRequest;

class GetStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'period.regex' => 'Period must be in YYYY-MM format.',
        ];
    }
}
