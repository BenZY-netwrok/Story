<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'owner_user_id' => $this->user()->id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            'title' => 'required|string|max:1000',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|string',
            'owner_user_id' => 'exists:users,id',
            'num' =>'nullable|int',
            'last_user_id' => 'nullable',
            'status' => 'required|boolean',
        ];
    }
}
