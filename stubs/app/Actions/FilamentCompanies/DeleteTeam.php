<?php

namespace App\Actions\FilamentCompanies;

use App\Models\Team;
use Wallo\FilamentCompanies\Contracts\DeletesCompanies;

class DeleteTeam implements DeletesCompanies
{
    /**
     * Delete the given company.
     */
    public function delete(Team $team): void
    {
        $team->purge();
    }
}
