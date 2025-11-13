<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class RejectExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reject', $this->route('expense'));
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }
}
