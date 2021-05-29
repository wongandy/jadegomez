<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleFormRequest extends FormRequest
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
            'items.*.item_id' => 'required|distinct',
            'items.*.quantity' => 'required|integer|min:1'
        ];
    }

    public function message()
    {
        return [
            'items.*.quantity' => 'Qty field is required.'
        ];
    }
}
