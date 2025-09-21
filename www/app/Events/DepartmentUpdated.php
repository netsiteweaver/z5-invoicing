<?php

namespace App\Events;

use App\Models\Department;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Department $department, public ?string $actor = null)
    {
    }
}

