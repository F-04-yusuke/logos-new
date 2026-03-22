<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|in:YouTube,X,記事,知恵袋,本,その他',
            'comment'      => 'nullable|string|max:5000',
            'is_published' => 'boolean',
        ];
    }
}
