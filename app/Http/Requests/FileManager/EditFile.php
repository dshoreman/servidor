<?php

namespace Servidor\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class EditFile extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required',
            'contents' => 'present',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File path must be specified.',
        ];
    }

    /**
     * @return array{file: string, contents: string}
     */
    public function validated(): array
    {
        $data = parent::validated();

        return [
            'file' => (string) $data['file'],
            'contents' => (string) $data['contents'],
        ];
    }
}
