<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'adress' => 'required',
        ];
    }

  public function messages()
  {
    return [
      'zipcode.required' => '郵便番号を入力してください',
      'zipcode.regex' => '郵便番号はハイフンありの8桁で入力してください （例）111-1111',
      'adress.required' => '住所を入力してください',
    ];
  }
}
