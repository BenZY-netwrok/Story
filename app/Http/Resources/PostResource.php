<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'owner_user_id' => $this->owner_user_id,
            'image_url' =>$this->image ? URL::to($this->image) : null,
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'status' => $this->status !== 'draft',
            'num' => $this->num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'floors' => [],
            'current_user_id' => $request->user()->id
        ];
    }
}
