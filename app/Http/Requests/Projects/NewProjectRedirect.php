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
            'config' => [
                'sometimes', 'required', 'array:' . implode(',', [
                    'redirectWww',
                    'redirect', 'redirect.target', 'redirect.type',
                    'ssl', 'sslCertificate', 'sslPrivateKey', 'sslRedirect',
                ]),
            ],
            'config.redirectWww' => 'sometimes|required|integer|between:-1,1',
            'config.redirect.target' => 'required|string',
            'config.redirect.type' => 'required|integer',
            'config.ssl' => 'sometimes|required|boolean',
            'config.sslCertificate' => 'sometimes|required|string|filled',
            'config.sslPrivateKey' => 'sometimes|required|string|filled',
            'config.sslRedirect' => 'sometimes|required|boolean',
            'includeWww' => 'boolean',
        ];
    }

    public function validated(): array
    {
        /** @var array{domain: string, config: ?array, includeWww: ?bool} $data */
        $data = parent::validated();

        $data['config'] ??= [];
        $data['domain_name'] = $data['domain'];
        $data['include_www'] = $data['includeWww'] ?? false;
        unset($data['domain'], $data['includeWww']);

        return $data;
    }
}
