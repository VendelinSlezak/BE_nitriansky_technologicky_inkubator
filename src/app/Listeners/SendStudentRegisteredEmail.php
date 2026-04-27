<?php

namespace App\Listeners;

use App\Events\StudentRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\NewStudentRegisteredMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Student;

class SendStudentRegisteredEmail
{
    /**
     * Create the event listener.
     */
    public function __construct(User $user, Student $student)
    {

    }

    /**
     * Handle the event.
     */
    public function handle(StudentRegistered $event): void
    {
        Mail::to("nti@bleskos.com")->queue(new NewStudentRegisteredMail($event->user, $event->student));
    }
}
