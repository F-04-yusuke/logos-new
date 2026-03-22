<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|max:255',
            'comment'      => 'nullable|string|max:2000',
            'is_published' => 'required|boolean',
        ];
    }
}
