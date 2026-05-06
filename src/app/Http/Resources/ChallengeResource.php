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
            'name' => $this->name,


            $this->mergeWhen($request->routeIs('challenges.*'), [
                'category' => $this->whenNotNull($this->program_a_categories?->title),
                'description' => $this->description,
                'reward' => $this->whenNotNull($this->reward),
            ]),
            $this->mergeWhen($request->routeIs('challenges.show'), [
                'skillsDescription' => $this->whenNotNull($this->program_a_categories?->description_of_skills),
                'proposal_file_id' => $this->proposal_file->url,
            ]),

            'admin_info' => $this->mergeWhen($request->user()?->isAdmin(), [
                'status' => $this->status,
                'teams' => $this->when($this->status === 'open', function() {
                    return $this->relationLoaded('teams')
                        ? $this->teams->count()
                        : $this->teams()->count();
                }),
            ]),
        ];
    }
}
