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
     * @return array{database: string}
     */
    public function validated(): array
    {
        /** @var array{database: string} $data */
        $data = parent::validated();

        return array_merge($data, ['database' => $data['database']]);
    }
}
