<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:160'], 'amount' => ['required', 'numeric', 'min:1'], 'category' => ['required', 'in:event,transport,food,equipment,social,administration,other'], 'spent_at' => ['required', 'date'], 'comment' => ['nullable', 'string', 'max:1000']];
    }
}