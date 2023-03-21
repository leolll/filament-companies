<?php

namespace App\Actions\FilamentCompanies;

use App\Models\Team;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Wallo\FilamentCompanies\Contracts\InvitesCompanyEmployees;
use Wallo\FilamentCompanies\Events\InvitingCompanyEmployee;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Mail\CompanyInvitation;
use Wallo\FilamentCompanies\Rules\Role;

class InviteTeamMember implements InvitesCompanyEmployees
{
    /**
     * Invite a new company employee to the given company.
     *
     * @throws AuthorizationException
     */
    public function invite(User $user, Team $team, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addTeamMember', $team);

        $this->validate($team, $email, $role);

        InvitingCompanyEmployee::dispatch($team, $email, $role);

        $invitation = $team->teamInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new TeamInvitation($invitation));
    }

    /**
     * Validate the invite employee operation.
     */
    protected function validate(Team $team, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($team), [
            'email.unique' => __('filament-companies::default.errors.employee_already_invited'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnCompany($company, $email)
        )->validateWithBag('addTeamMember');
    }

    /**
     * Get the validation rules for inviting a company employee.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(Team $team): array
    {
        return array_filter([
            'email' => [
                'required', 'email',
                Rule::unique('company_invitations')->where(function (Builder $query) use ($team) {
                    $query->where('company_id', $team->id);
                }),
            ],
            'role' => FilamentCompanies::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the employee is not already on the company.
     */
    protected function ensureUserIsNotAlreadyOnCompany(Team $team, string $email): Closure
    {
        return static function ($validator) use ($team, $email) {
            $validator->errors()->addIf(
                $team->hasUserWithEmail($email),
                'email',
                __('filament-companies::default.errors.employee_already_belongs_to_company')
            );
        };
    }
}
