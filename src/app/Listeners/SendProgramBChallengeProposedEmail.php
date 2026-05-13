<?php

namespace App\Listeners;

use App\Events\ProgramBChallengeProposed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewProgramBChallengeMail;

class SendProgramBChallengeProposedEmail
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
    public function handle(ProgramBChallengeProposed $event): void
    {
        Mail::to("nti@bleskos.com")->queue(new NewProgramBChallengeMail($event->challenge));
    }
}
