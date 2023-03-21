<div class="flex justify-end">
    @if (Wallo\FilamentCompanies\FilamentCompanies::hasTeamFeatures())
        <x-filament::dropdown placement="bottom-start">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button @class([
                        'inline-flex items-center px-3 py-2 border border-transparent text-sm text-gray-800 leading-4 font-medium rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition',
                        'dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-white dark:hover:border-primary-400 dark:text-white dark:hover:text-primary-400' => config(
                            'filament.dark_mode'),
                    ])>
                        {{ Auth::user()->currentTeam->name }}
                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                    </button>
                </span>
            </x-slot>



            <!-- Company Settings -->
            <x-filament::dropdown.list>
                <!-- Company Management -->
                <div class="block px-2 py-2 text-xs text-gray-400">
                    Manage Company
                </div>

                <x-filament::dropdown.list.item
                    href="{{ url(\Wallo\FilamentCompanies\Pages\Teams\TeamSettings::getUrl(['team' => Auth::user()->currentTeam->id])) }}"
                    tag="a">
                    <div class="flex items-center">
                        {{ __('filament-companies::default.navigation.links.company_settings') }}
                    </div>
                </x-filament::dropdown.list.item>

                @can('create', Wallo\FilamentCompanies\FilamentCompanies::newCompanyModel())
                    <x-filament::dropdown.list.item
                        href="{{ url(\Wallo\FilamentCompanies\Pages\Teams\CreateTeam::getUrl()) }}" tag="a">
                        <div class="flex items-center">
                            {{ __('filament-companies::default.navigation.links.new_company') }}
                        </div>
                    </x-filament::dropdown.list.item>
                @endcan

                <x-filament::hr />

                <!-- Company Management -->
                <div class="block px-2 py-2 text-xs text-gray-400">
                    Switch Companies
                </div>

                <!-- Company Switcher -->
                @foreach (Auth::user()->allTeams() as $company)
                    <x-filament-companies::switchable-company :company="$company" />
                @endforeach
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    @endif
</div>
