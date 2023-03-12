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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @suppress PhanUnusedPublicMethodParameter
     *
     * @param array|int|string|null $key
     * @param mixed                 $default
     *
     * @return array{file: string, contents: string}
     */
    public function validated($key = null, $default = null): array
    {
        $data = (array) parent::validated();

        return [
            'file' => (string) $data['file'],
            'contents' => (string) $data['contents'],
        ];
    }
}
