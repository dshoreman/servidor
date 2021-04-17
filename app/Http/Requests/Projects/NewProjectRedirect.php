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

    public function validated(): array
    {
        $data = parent::validated();

        $data['domain_name'] = (string) $data['domain'];
        unset($data['domain']);

        return $data;
    }
}
