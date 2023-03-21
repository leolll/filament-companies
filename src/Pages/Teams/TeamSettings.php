<?php

namespace Wallo\FilamentCompanies\Pages\Teams;

use App\Models\Team;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Wallo\FilamentCompanies\FilamentCompanies;

class TeamSettings extends Page
{
    public Team $company;

    protected static string $view = 'filament-companies::filament.pages.teams.team_settings';

    protected static bool $shouldRegisterNavigation = false;

    protected function getTitle(): string
    {
        return __('filament-companies::default.pages.titles.company_settings');
    }

    public function mount(Team $company): void
    {
        abort_unless(FilamentCompanies::hasTeamFeatures(), 403);
        abort_if(Gate::denies('view', $company), 403);
        $this->company = Auth::user()->currentTeam;
    }

    public static function getSlug(): string
    {
        return 'teams/{team}';
    }
}
