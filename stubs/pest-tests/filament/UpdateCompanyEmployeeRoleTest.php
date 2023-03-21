<?php

use App\Models\User;
use Livewire\Livewire;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;

test('company employee roles can be updated', function () {
    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->set('managingRoleFor', $otherUser)
                    ->set('currentRole', 'editor')
                    ->call('updateRole');

    expect($otherUser->fresh()->hasCompanyRole(
        $user->currentTeam->fresh(), 'editor'
    ))->toBeTrue();
});

test('only company owner can update company employee roles', function () {
    $user = User::factory()->withPersonalCompany()->create();

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->set('managingRoleFor', $otherUser)
                    ->set('currentRole', 'editor')
                    ->call('updateRole')
                    ->assertStatus(403);

    expect($otherUser->fresh()->hasCompanyRole(
        $user->currentTeam->fresh(), 'admin'
    ))->toBeTrue();
});
