@props(['submit'])

<div {{ $attributes->class(['grid grid-cols-1 md:grid-cols-3 gap-6']) }}>

    <div class="col-span-1 flex justify-between">
        <div class="px-4 sm:px-0">
            <h3 @class([
                'text-lg font-medium text-gray-900 filament-companies::grid-title',
                'dark:text-white' => config('filament.dark_mode'),
            ])>{{ $title }}</h3>

            <p @class([
                'mt-1 text-sm text-gray-600 dark:text-gray-400 filament-companies::grid-description',
                'dark:text-gray-100' => config('filament.dark_mode'),
            ])>
                {{ $description }}
            </p>
        </div>
    </div>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit.prevent="{{ $submit }}">
            <x-filament::card>
                <div>
                    {{ $form }}
                </div>
            </x-filament::card>

            @if (isset($actions))
                <div class="flex items-center justify-start gap-4 py-3 text-left">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
