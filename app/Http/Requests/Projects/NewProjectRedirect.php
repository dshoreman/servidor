<?php

namespace Servidor\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;
use Servidor\Rules\Domain;

class NewProjectRedirect extends FormRequest
{
    public function rules(): array
    {
        return [
            'domain' => ['required', new Domain()],
            'target' => 'required|string',
            'type' => 'required|integer',
        ];
    }
}
