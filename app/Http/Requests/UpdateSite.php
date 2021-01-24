<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSite extends FormRequest
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
            'redirect_type' => 'required_if:type,redirect|nullable|integer',
            'redirect_to' => 'required_if:type,redirect|nullable|string',
        ];
    }
}
