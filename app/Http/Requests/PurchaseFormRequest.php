<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseFormRequest extends FormRequest
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
            // 'items.*.serial_number.*' => 'bail|required|unique:item_purchase,serial_number|distinct',
            'items.*.serial_number.*' => ['distinct', Rule::unique('item_purchase', 'serial_number')->where(function ($query) {
                return $query->where('status', 'available');
            })],
            'items.*.quantity' => 'required|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'items.*.serial_number.*.unique' => 'Serial Number :input cannot be used again',
            'items.*.serial_number.*.distinct' => 'Serial Number :input exists in another field.',
            'items.*.quantity' => 'Qty field is required.'
        ];
    }
}
