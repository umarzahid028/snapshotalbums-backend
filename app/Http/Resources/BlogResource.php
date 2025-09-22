<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'excerpt' => $this->excerpt,
            'image' => $this->image,
            'author' => [
                'name' => $this->author,
                'email' => $this->author_email,
            ],
            'status' => $this->status,
            'category' => $this->category,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ],
        ];
    }
}
