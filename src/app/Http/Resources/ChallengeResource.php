<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
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
            'program' => $this->program,
            'category' => $this->whenNotNull($this->program_a_categories?->title),
            'skillsDescription' => $this->whenNotNull($this->program_a_categories?->description_of_skills),
            'name' => $this->name,
            'description' => $this->description,
            'reward' => $this->whenNotNull($this->reward),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments'))
        ];
    }
}
