<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return ['amount' => ['required', 'numeric', 'min:1'], 'method' => ['required', 'in:cash,wave,orange_money,free_money,transfer,other'], 'reference' => ['nullable', 'string', 'max:100'], 'paid_at' => ['required', 'date'], 'comment' => ['nullable', 'string', 'max:1000']];
    }
}