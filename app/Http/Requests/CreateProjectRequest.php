<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Servidor\Rules\Domain;

class CreateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:projects,name',
            'applications' => 'array',
            'applications.*.template' => 'required|in:html,php,laravel',
            'applications.*.domain' => [new Domain()],
            'applications.*.provider' => 'required|in:github,bitbucket',
            'applications.*.repository' => 'required|nullable|regex:_^([a-z-]+)/([a-z-]+)$_i',
            'applications.*.branch' => 'nullable|string',
            'is_enabled' => 'boolean',
        ];
    }
}
