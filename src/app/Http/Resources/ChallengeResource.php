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
            'title' => $this->name,


            $this->mergeWhen($request->routeIs('challenges.*'), [
                'category' => $this->whenNotNull($this->program_a_categories?->title),
                'description' => $this->description,
                'reward' => $this->whenNotNull($this->reward),
            ]),

            $this->mergeWhen($request->routeIs('challenges.registration-requests'),[
                'name_of_author' => $this->users->name,
                'technical_specification_url' => $this->files->url,
                'technical_specification_name' => $this->files->original_name,
                'when' => $this->created_at->format('d.m.Y H:i')
            ]),

            $this->mergeWhen($request->routeIs('challenges.show'), [
                'skillsDescription' => $this->whenNotNull($this->program_a_categories?->description_of_skills),
                'proposal_file_url' => $this->proposal_file->url,
                'proposal_file_name' => $this->proposal_file->original_name,
                'proposal_file_size' => $this->proposal_file->size,
            ]),

            $this->mergeWhen($request->user()?->isAdmin() && !$request->routeIs('challenges.registration-requests'), [
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
