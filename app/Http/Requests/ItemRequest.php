<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'name' => 'required|unique:items',
            'selling_price' => 'required',
            'with_serial_number' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => ':input taken boi',
            'name.required' => ':input please input something boi'
        ];
    }
}
