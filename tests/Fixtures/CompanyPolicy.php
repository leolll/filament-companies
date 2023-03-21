<?php

namespace Wallo\FilamentCompanies\Tests\Fixtures;

use App\Models\Team;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function view(User $user, Team $team)
    {
        return $user->belongsTeam($company);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function update(User $user, Team $team)
    {
        return $user->ownsTeam($company);
    }

    /**
     * Determine whether the user can add company employees.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function addTeamMember(User $user, Team $team)
    {
        return $user->ownsTeam($company);
    }

    /**
     * Determine whether the user can update company employee permissions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function updateTeamMember(User $user, Team $team)
    {
        return $user->ownsTeam($company);
    }

    /**
     * Determine whether the user can remove company employees.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function removeCompanyEmployee(User $user, Team $team)
    {
        return $user->ownsTeam($company);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $user, Team $team)
    {
        return $user->ownsTeam($company);
    }
}
