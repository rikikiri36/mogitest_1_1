<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'payment' => 'required',
            'zipcode' => 'required',
            'adress' => 'required',
        ];
    }

    public function messages()
    {
      return [
        'payment.required' => '支払い方法を選択してください',
        'zipcode.required' => '配送先の郵便番号を登録してください',
        'adress.required' => '配送先の住所を登録してください',
      ];
    }  
}
