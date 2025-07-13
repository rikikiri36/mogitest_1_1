<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'detail' => 'required|max:400',
            'image' => 'mimes:jpeg,png',
        ];
    }

    public function messages()
    {
    return [
      'detail.required' => '本文を入力してください',
      'detail.max' => '本文は400文字以内で入力してください',
      'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
    ];
    }
}
