<?php

namespace App\Listeners;

use App\Events\StudentInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendStudentInvitationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentInvited $event): void
    {
        Mail::to($event->invitedStudentEmail)->queue(new StudentInvitedToTeamMail($event->team));
    }
}
