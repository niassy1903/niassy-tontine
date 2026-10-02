<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:3', 'max:3000'],
            'type' => ['required', 'in:normal,announcement,important'],
        ];
    }
}