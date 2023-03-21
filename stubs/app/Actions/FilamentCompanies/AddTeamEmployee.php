<?php

namespace App\Actions\FilamentCompanies;

use App\Models\Team;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Wallo\FilamentCompanies\Contracts\AddsCompanyEmployees;
use Wallo\FilamentCompanies\Events\AddingCompanyEmployee;
use Wallo\FilamentCompanies\Events\CompanyEmployeeAdded;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Rules\Role;

class AddTeamEmployee implements AddsCompanyEmployees
{
    /**
     * Add a new company employee to the given company.
     *
     * @throws AuthorizationException
     */
    public function add(User $user, Team $team, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addTeamMember', $company);

        $this->validate($company, $email, $role);

        $newCompanyEmployee = FilamentCompanies::findUserByEmailOrFail($email);

        AddingCompanyEmployee::dispatch($company, $newCompanyEmployee);

        $company->users()->attach(
            $newCompanyEmployee, ['role' => $role]
        );

        CompanyEmployeeAdded::dispatch($company, $newCompanyEmployee);
    }

    /**
     * Validate the add employee operation.
     */
    protected function validate(Team $team, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('filament-companies::default.errors.email_not_found'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnCompany($company, $email)
        )->validateWithBag('addTeamMember');
    }

    /**
     * Get the validation rules for adding a company employee.
     *
     * @return array<string, Rule|array|string>
     */
    protected function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
            'role' => FilamentCompanies::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the company.
     */
    protected function ensureUserIsNotAlreadyOnCompany(Team $team, string $email): Closure
    {
        return static function ($validator) use ($company, $email) {
            $validator->errors()->addIf(
                $company->hasUserWithEmail($email),
                'email',
                __('filament-companies::default.errors.user_belongs_to_company')
            );
        };
    }
}
