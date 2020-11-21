<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProject extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:sites,name',
            'applications' => 'array',
            'is_enabled' => 'boolean',
        ];
    }
}
