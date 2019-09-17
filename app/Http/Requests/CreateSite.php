<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Servidor\Rules\Domain;

class CreateSite extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:sites,name',
            'primary_domain' => [new Domain()],
            'is_enabled' => 'boolean',
        ];
    }
}
