<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AiAssistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt'  => 'required|string|max:5000',
            'context' => 'nullable|string|max:10000',
        ];
    }
}
