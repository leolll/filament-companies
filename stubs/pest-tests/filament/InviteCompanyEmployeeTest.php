<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Wallo\FilamentCompanies\Features;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;
use Wallo\FilamentCompanies\Mail\CompanyInvitation;

test('company employees can be invited to company', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->set('addTeamMemberForm', [
                        'email' => 'test@example.com',
                        'role' => 'admin',
                    ])->call('addTeamMember');

    Mail::assertSent(CompanyInvitation::class);

    expect($user->currentTeam->fresh()->companyInvitations)->toHaveCount(1);
})->skip(function () {
    return ! Features::sendsCompanyInvitations();
}, 'Company invitations not enabled.');

test('company employee invitations can be cancelled', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    // Add the company employee...
    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->set('addTeamMemberForm', [
                        'email' => 'test@example.com',
                        'role' => 'admin',
                    ])->call('addTeamMember');

    $invitationId = $user->currentTeam->fresh()->companyInvitations->first()->id;

    // Cancel the company invitation...
    $component->call('cancelCompanyInvitation', $invitationId);

    expect($user->currentTeam->fresh()->companyInvitations)->toHaveCount(0);
})->skip(function () {
    return ! Features::sendsCompanyInvitations();
}, 'Company invitations not enabled.');
