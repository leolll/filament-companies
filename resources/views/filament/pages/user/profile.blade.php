<x-filament::page>
    <div>
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire(\Wallo\FilamentCompanies\Http\Livewire\UpdateProfileInformationForm::class)

            <x-filament-companies::section-border />
        @endif

        @if (!is_null($user->password) && Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            @livewire(\Wallo\FilamentCompanies\Http\Livewire\UpdatePasswordForm::class)

            <x-filament-companies::section-border />
        @else
            @livewire(\Wallo\FilamentCompanies\Http\Livewire\SetPasswordForm::class)

            <x-filament-companies::section-border />
        @endif

        @if (!is_null($user->password) && Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            @livewire(\Wallo\FilamentCompanies\Http\Livewire\TwoFactorAuthenticationForm::class)

            <x-filament-companies::section-border />
        @endif

        @if (Wallo\FilamentCompanies\Socialite::hasSocialiteFeatures())
            @livewire(\Wallo\FilamentCompanies\Http\Livewire\ConnectedAccountsForm::class)


            <x-filament-companies::section-border />
        @endif

        @if (!is_null($user->password))
            @livewire(\Wallo\FilamentCompanies\Http\Livewire\LogoutOtherBrowserSessionsForm::class)
        @endif

        @if (!is_null($user->password) && Wallo\FilamentCompanies\FilamentCompanies::hasAccountDeletionFeatures())
            <x-filament-companies::section-border />

            @livewire(\Wallo\FilamentCompanies\Http\Livewire\DeleteUserForm::class)
        @endif
    </div>
</x-filament::page>
