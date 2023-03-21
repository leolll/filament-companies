<?php

use App\Models\User;
use Livewire\Livewire;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;

test('company employees can be removed from companies', function () {
    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->set('companyEmployeeIdBeingRemoved', $otherUser->id)
                    ->call('removeCompanyEmployee');

    expect($user->currentTeam->fresh()->users)->toHaveCount(0);
});

test('only company owner can remove company employees', function () {
    $user = User::factory()->withPersonalCompany()->create();

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->set('companyEmployeeIdBeingRemoved', $user->id)
                    ->call('removeCompanyEmployee')
                    ->assertStatus(403);
});
