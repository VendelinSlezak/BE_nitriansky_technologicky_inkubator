<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'perex' => $this->perex,
            'content' => $this->content,
            'author' => $this->user->name,
            'image_url' => asset('storage/' . $this->image->path),
            'image_description' => $this->image_description,
            'created_at' => $this->created_at,
        ];
    }
}
