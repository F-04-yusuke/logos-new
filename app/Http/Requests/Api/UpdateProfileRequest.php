<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $canChangeName = !$user->name_updated_at ||
            Carbon::parse($user->name_updated_at)->addDays(7)->isPast();

        $rules = [
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ];

        if ($canChangeName) {
            $rules['name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
