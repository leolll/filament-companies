<x-filament::page>
    <div>
        @livewire(\Wallo\FilamentCompanies\Http\Livewire\UpdateTeamNameForm::class, ['company' => $company])

        @livewire(\Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager::class, ['company' => $company])

        @if (!$company->personal_team && Gate::check('delete', $company))
            <x-filament-companies::section-border />

            <div class="mt-10 sm:mt-0">
                @livewire(\Wallo\FilamentCompanies\Http\Livewire\DeleteTeamForm::class, ['company' => $company])
            </div>
        @endif
    </div>
</x-filament::page>
