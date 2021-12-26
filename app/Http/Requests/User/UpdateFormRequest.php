<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'uuid', 'exists:users,id'],
            'name' => ['nullable', 'string'],          
            'status' => ['nullable', 'nullable'],
            'avatar' => ['nullable', 'string'] 
        ];
    }
}
