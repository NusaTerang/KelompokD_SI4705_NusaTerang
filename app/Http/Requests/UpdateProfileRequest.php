<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $id = $this->user()->getKey();

        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . $id . ',id_donatur'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ];
    }
}
