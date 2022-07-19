<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(preg_match('/^api\/reply\/\d+\/reply$/i', $this->path())) {
            return true;
        }
        $post = $this->route('post');
        if ($this->user()->id !== $post->owner_user_id) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if(preg_match('/^api\/reply\/\d+\/reply$/i', $this->path())) {
            return [
                'title' => 'required|string|max:1000',
                'image' => 'nullable|string',
                'owner_user_id' => 'exists:users,id',
                'num' =>'nullable|int',
                'status' => 'required|boolean',
                'content' => 'nullable|string',
                'floors' => 'array'
            ];
        }else {
            return [
                'title' => 'required|string|max:1000',
                'image' => 'nullable|string',
                'owner_user_id' => 'exists:users,id',
                'num' =>'nullable|int',
                'status' => 'required|boolean',
                'content' => 'nullable|string',
                'floors' => 'array'
            ];
        }

    }
}
