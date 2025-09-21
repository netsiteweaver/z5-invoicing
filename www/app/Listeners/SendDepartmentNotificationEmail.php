<?php

namespace App\Listeners;

use App\Events\DepartmentCreated;
use App\Events\DepartmentUpdated;
use App\Events\DepartmentDeleted;
use App\Mail\GenericNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendDepartmentNotificationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $department = $event->department;
        $actor = $event->actor ?? 'System';

        if ($event instanceof DepartmentCreated) {
            $subject = 'Department created: ' . $department->name;
            $body = "A new department has been created by {$actor}.\n\n" .
                "Name: {$department->name}\n" .
                ($department->manager?->name ? ("Manager: {$department->manager->name}\n") : '') .
                ($department->email ? ("Email: {$department->email}\n") : '') .
                ($department->phone_number ? ("Phone: {$department->phone_number}\n") : '');
        } elseif ($event instanceof DepartmentUpdated) {
            $subject = 'Department updated: ' . $department->name;
            $body = "Department has been updated by {$actor}.\n\nName: {$department->name}";
        } elseif ($event instanceof DepartmentDeleted) {
            $subject = 'Department deleted: ' . $department->name;
            $body = "Department has been deleted by {$actor}.\n\nName: {$department->name}";
        } else {
            return;
        }

        // Send to manager if present, or fallback to app from address
        $toEmail = $department->manager?->email ?? config('mail.from.address');

        if ($toEmail) {
            Mail::to($toEmail)->queue(new GenericNotificationMail($subject, $body));
        }
    }
}

