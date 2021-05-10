<?php

namespace Servidor\Http\Requests\Databases;

use Illuminate\Foundation\Http\FormRequest;

class NewDatabase extends FormRequest
{
    public function rules(): array
    {
        return [
            'database' => 'required|string',
        ];
    }

    /**
     * @return array{name: string}
     */
    public function validated(): array
    {
        return [
            'name' => (string) parent::validated()['database'],
        ];
    }
}
