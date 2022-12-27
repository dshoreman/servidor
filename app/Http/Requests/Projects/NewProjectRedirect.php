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
            'includeWww' => 'boolean',
            'target' => 'required|string',
            'type' => 'required|integer',
        ];
    }

    public function validated(): array
    {
        $data = parent::validated();

        $data['domain_name'] = (string) ($data['domain'] ?? '');
        $data['include_www'] = (bool) ($data['includeWww'] ?? false);
        unset($data['domain'], $data['includeWww']);

        return $data;
    }
}
