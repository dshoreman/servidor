<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:projects,name',
            'is_enabled' => 'boolean',
        ];
    }
}
