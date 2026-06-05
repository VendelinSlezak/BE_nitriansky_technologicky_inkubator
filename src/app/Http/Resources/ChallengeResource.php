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

            $this->mergeWhen($request->routeIs(['challenges.*', 'challenge-get']), [
                'category' => $this->whenNotNull($this->program_a_categories?->title),
                'description' => $this->description,
                'reward' => $this->whenNotNull($this->reward),
            ]),

            $this->mergeWhen($request->routeIs(['challenges.registration-requests', 'challenge-get']), [
                'name_of_author' => $this->users?->name,
                'technical_specification_url' => $this->files?->url,
                'technical_specification_name' => $this->files?->original_name,
                $this->mergeWhen($request->routeIs('challenges.registration-requests'), [
                    'when' => $this->created_at?->format('d.m.Y H:i')
                ])
            ]),

            $this->mergeWhen($request->routeIs('challenges.show'), [
                'skillsDescription' => $this->whenNotNull($this->program_a_categories?->description_of_skills),
                'proposal_file_url' => $this->proposal_file?->url,
                'proposal_file_name' => $this->proposal_file?->original_name,
                'proposal_file_size' => $this->proposal_file?->size,
            ]),

            $this->mergeWhen($request->user()?->isAdmin() && !$request->routeIs('challenges.registration-requests'), [
                'status' => $this->status,
                'teams' => $this->when(
                    $this->status === 'open',
                    function() {
                    return $this->relationLoaded('teams')
                        ? $this->teams->count()
                        : $this->teams()->count();
                    },
                    1),
            ]),

            $this->mergeWhen($request->routeIs('challenge-get') && $this->status == 'open', [
                'all_teams' => $this->teams->map(function($team) {
                    return [
                        'id' => $team->id,
                        'name_of_team' => $team->name,
                        'teamleader_email' => $team->teamMembers?->firstWhere('pivot.status', 'teamleader')?->user?->email,
                        'number_of_members' => $team->teamMembers?->count() ?? 0,
                    ];
                })->toArray()
            ]),

            $this->mergeWhen($request->routeIs('challenge-get') && ($this->status == 'in_evaluation' || $this->status == 'accepted_by_commission' || $this->status == 'rejected_by_commission'), [
                'commission_decision' => $this->commission_comment,
                'mentor_email' => $this->mentors?->user?->email,
                'milestones' => [],
            ]),

            $this->mergeWhen($request->routeIs('challenge-get') && $this->status == 'in_progress', [
                'milestones' => $this->milestones->map(function($milestone) {
                    return [
                        'id' => $milestone->id,
                        'date_of_completion' => $milestone->date_of_reasisation,
                        'title' => $milestone->title,
                        'description' => $milestone->description,
                        'comment_from_mentor' => $milestone->comment,
                    ];
                })->toArray(),
                'evaluation_comment' => [],
            ]),

            $this->mergeWhen($request->routeIs('challenge-get') && $this->status == 'finished', [
                'evaluation_comment' => $this->final_assessment
            ])
        ];
    }
}
