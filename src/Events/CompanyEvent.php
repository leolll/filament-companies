<?php

namespace Wallo\FilamentCompanies\Events;

use App\Models\Team;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class CompanyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The company instance.
     */
    public Team $team;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->company = $company;
    }
}
