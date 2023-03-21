<?php

namespace Wallo\FilamentCompanies\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Filament\Notifications\Notification;
use Wallo\FilamentCompanies\RedirectsActions;
use Illuminate\Contracts\Auth\Authenticatable;
use Wallo\FilamentCompanies\Contracts\CreatesTeams;

class CreateTeamForm extends Component
{
    use RedirectsActions;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * Create a new company.
     */
    public function createTeam(CreatesTeams $creator): Response|Redirector|RedirectResponse
    {
        $this->resetErrorBag();

        $creator->create(Auth::user(), $this->state);

        $name = $this->state['name'];

        $this->teamCreated($name);

        return $this->redirectPath($creator);
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): User|Authenticatable|null
    {
        return Auth::user();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('filament-companies::teams.create-team-form');
    }

    public function companyCreated($name): void
    {
        Notification::make()
            ->title(__('filament-companies::default.notifications.company_created.title'))
            ->success()
            ->body(__('filament-companies::default.notifications.company_created.body', ['name' => $name]))
            ->send();
    }
}
