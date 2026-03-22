<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'content'          => 'required|string|max:20000',
            'category_ids'     => 'required|array|min:1|max:2',
            'category_ids.*'   => 'integer|exists:categories,id',
            'timeline'         => 'nullable|array',
            'timeline.*.date'  => 'nullable|string|max:255',
            'timeline.*.event' => 'nullable|string|max:1000',
            'timeline.*.is_ai' => 'nullable|boolean',
        ];
    }
}
