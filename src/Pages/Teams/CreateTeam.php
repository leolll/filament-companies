<?php

namespace Wallo\FilamentCompanies\Pages\Teams;

use Filament\Pages\Page;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Wallo\FilamentCompanies\FilamentCompanies;

class CreateTeam extends Page
{
    public Team $company;

    protected static string $view = 'filament-companies::filament.pages.teams.create_team';

    protected static bool $shouldRegisterNavigation = false;

    protected function getTitle(): string
    {
        return __('filament-companies::default.pages.titles.create_company');
    }

    public function mount(Team $company): void
    {
        abort_unless(FilamentCompanies::hasTeamFeatures(), 403);
        Gate::authorize('create', FilamentCompanies::newCompanyModel());
        $this->company = Auth::user()->currentTeam;
    }

    public static function getSlug(): string
    {
        return 'teams/create';
    }
}
