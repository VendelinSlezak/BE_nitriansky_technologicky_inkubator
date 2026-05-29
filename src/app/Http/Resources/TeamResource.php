<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team_id' => $this->id,
            'challenge_id' => $this->challenge_id,
            'name' => $this->name,
            'members' => $this->teamMembers->map(function ($student) {
                return [
                    'id' => $student->id,
                    'status' => $student->pivot->status,
                ];
            }),
            'proposal_of_implementation_id' => $this->proposal_of_implementation_id,
            'cover_letter_id' => $this->cover_letter_id
        ];
    }
}
