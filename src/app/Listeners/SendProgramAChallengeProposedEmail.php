<?php

namespace App\Listeners;

use App\Events\ProgramAChallengeProposed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\NewProgramAChallengeMail;
use Illuminate\Support\Facades\Mail;

class SendProgramAChallengeProposedEmail
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
    public function handle(ProgramAChallengeProposed $event): void
    {
        Mail::to("nti@bleskos.com")->queue(new NewProgramAChallengeMail($event->challenge));
    }
}
