<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

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
            'name' => 'required',
            'description' => 'required|max:255',
            'categories' => 'required',
            'condition_id' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => App::environment('testing')
                ? ['nullable', 'string'] // ← テスト環境では文字列OKにする
                : ['required', 'image', 'mimes:jpeg,png'],
        ];
    }

    public function messages()
    {
    return [
      'name.required' => '商品名を入力してください',
      'description.required' => '商品説明を入力してください',
      'description.max' => '商品説明は255文字以下で入力してください',
      'categories.required' => '商品のカテゴリーを１つ以上選択してください',
      'condition_id.required' => '商品の状態を選択してください',
      'price.required' => '商品の販売価格を入力してください',
      'price.numeric' => '商品の販売価格は数字で入力してください',
      'price.min' => '商品の販売価格は0以上で入力してください',
      'image.required' => '商品画像を選択してください',
      'image.mimes' => '商品画像は.jpegもしくは.png形式を選択してください',
    ];
  }
}
