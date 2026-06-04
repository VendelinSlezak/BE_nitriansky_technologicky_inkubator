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
                            'email' => $student->user->email,
                            'status' => $student->pivot->status,
                        ];
                    }),
                    'proposal_of_implementation_url' => $this->proposal_of_implementation->url,
                    'proposal_of_implementation_name' => $this->proposal_of_implementation->original_name,
                    'cover_letter_url' => $this->cover_letter->url,
                    'cover_letter_name' => $this->cover_letter->original_name,
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
