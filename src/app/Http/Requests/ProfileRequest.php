<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'image' => 'mimes:jpeg,png',
            'name' => 'required',
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'adress' => 'required',
        ];
    }

  public function messages()
  {
    return [
      'name.required' => '名前を入力してください',
      'zipcode.required' => '郵便番号を入力してください',
      'zipcode.regex' => '郵便番号はハイフンありの8桁で入力してください （例）111-1111',
      'adress.required' => '住所を入力してください',
      'image.mimes' => 'プロフィール画像は.jpegもしくは.png形式を選択してください',
    ];
  }
}
