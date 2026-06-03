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
            'name' => $this->name,
            $this->mergeWhen(!$request->routeIs('admin.teams'), function () {
                return [
                    'challenge_id' => $this->challenge_id,
                    'members' => $this->teamMembers->map(function ($student) {
                        return [
                            'id' => $student->id,
                            'status' => $student->pivot->status,
                        ];
                    }),
                    'proposal_of_implementation_id' => $this->proposal_of_implementation_id,
                    'cover_letter_id' => $this->cover_letter_id
                    ];
                }),
            $this->mergeWhen($request->routeIs('admin.teams'), function () {
                $teamleader = $this->teamMembers->firstWhere('pivot.status', 'teamleader');

                return [
                    'teamleader_name' => $teamleader?->user?->name,
                    'is_active' => is_null($this->active_to) || $this->active_to > now()->toDateTimeString(),
                ];
            })
        ];
    }
}
