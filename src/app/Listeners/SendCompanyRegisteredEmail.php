<?php

namespace App\Listeners;

use App\Events\CompanyRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewCompanyRegisteredMail;

class SendCompanyRegisteredEmail
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
    public function handle(CompanyRegistered $event): void
    {
        Mail::to("nti@bleskos.com")->queue(new NewCompanyRegisteredMail($event->user, $event->company));
    }
}
