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
            'config' => 'sometimes|required|array:ssl,sslCertificate,sslPrivateKey',
            'config.ssl' => 'sometimes|required|boolean',
            'config.sslCertificate' => 'sometimes|required|string|filled',
            'config.sslPrivateKey' => 'sometimes|required|string|filled',
            'includeWww' => 'boolean',
            'target' => 'required|string',
            'type' => 'required|integer',
        ];
    }

    public function validated(): array
    {
        /** @var array{domain: string, config: ?array, includeWww: ?bool,
         *             target: string, type: int} $data */
        $data = parent::validated();

        $data['config'] ??= [];
        $data['domain_name'] = $data['domain'];
        $data['include_www'] = $data['includeWww'] ?? false;
        unset($data['domain'], $data['includeWww']);

        return $data;
    }
}
