<?php

use Illuminate\Support\Facades\Route;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Http\Controllers\CompanyInvitationController;
use Wallo\FilamentCompanies\Http\Controllers\CurrentCompanyController;
use Wallo\FilamentCompanies\Http\Controllers\Livewire\PrivacyPolicyController;
use Wallo\FilamentCompanies\Http\Controllers\Livewire\TermsOfServiceController;
use Wallo\FilamentCompanies\Http\Controllers\OAuthController;
use Wallo\FilamentCompanies\Pages\Teams\CreateTeam;
use Wallo\FilamentCompanies\Pages\Teams\TeamSettings;
use Wallo\FilamentCompanies\Pages\User\APITokens;
use Wallo\FilamentCompanies\Pages\User\Profile;
use Wallo\FilamentCompanies\Socialite;

Route::group(['middleware' => config('filament.middleware.base', ['web'])], static function () {
    if (Socialite::hasSocialiteFeatures()) {
        Route::get('/oauth/{provider}', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
        Route::get('/oauth/{provider}/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
    }

    if (FilamentCompanies::hasTermsAndPrivacyPolicyFeature()) {
        Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])->name('terms.show');
        Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');
    }

    $authMiddleware = config('jetstream.guard')
            ? 'auth:'.config('jetstream.guard')
            : 'auth';

    $authSessionMiddleware = config('jetstream.auth_session', false)
            ? config('jetstream.auth_session')
            : null;

    Route::prefix(config('filament.path'))
        ->group(function () {
            Route::get('/user/profile', Profile::class);

            Route::group(['middleware' => 'verified'], static function () {
                // API...
                if (FilamentCompanies::hasApiFeatures()) {
                    Route::get('/user/api-tokens', APITokens::class);
                }

                // Companies...
                if (FilamentCompanies::hasTeamFeatures()) {
                    Route::get('teams/create', CreateTeam::class);

                    Route::get('teams/{team}', TeamSettings::class);
                    Route::put('/current-team', [CurrentCompanyController::class, 'update'])->name('current-company.update');

                    Route::get('/team-invitations/{invitation}', [CompanyInvitationController::class, 'accept'])
                        ->middleware(['signed'])
                        ->name('company-invitations.accept');
                }
            });
        });
});
