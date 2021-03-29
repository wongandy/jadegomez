<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFormRequest extends FormRequest
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
            'report_type' => 'required',
            'date_range' => 'required',
            'items.*.serial_number.*' => 'bail|required|unique:item_purchase,serial_number|distinct'
        ];
    }

    public function messages()
    {
        return [
            'items.*.serial_number.*.unique' => ':input has already been used',
            'items.*.serial_number.*.distinct' => ':input is a duplicate',
        ];
    }
}
