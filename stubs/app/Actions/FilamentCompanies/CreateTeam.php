<?php

namespace App\Actions\FilamentCompanies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Events\AddingCompany;
use Illuminate\Auth\Access\AuthorizationException;
use Wallo\FilamentCompanies\Contracts\CreatesTeams;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new company for the given user.
     *
     * @param  array<string, string>  $input
     *
     * @throws AuthorizationException
     */
    public function create(User $user, array $input): Team
    {
        Gate::forUser($user)->authorize('create', FilamentCompanies::newCompanyModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createCompany');

        AddingCompany::dispatch($user);

        $user->switchTeam($team = $user->ownedTeams()->create([
            'name' => $input['name'],
            'personal_team' => false,
        ]));

        return $team;
    }
}
