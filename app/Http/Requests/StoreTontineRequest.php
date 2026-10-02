<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTontineRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:2000'], 'type' => ['required', 'in:dahira,association,asc,famille,amis,professionnel,autre'], 'visibility' => ['required', 'in:private,public'], 'frequency' => ['required', 'in:weekly,monthly,quarterly,yearly'], 'contribution_amount' => ['required', 'numeric', 'min:0'], 'starts_at' => ['required', 'date'], 'rules' => ['nullable', 'string', 'max:4000']];
    }
}