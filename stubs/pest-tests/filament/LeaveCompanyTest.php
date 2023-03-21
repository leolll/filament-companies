<?php

use App\Models\User;
use Livewire\Livewire;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;

test('users can leave companies', function () {
    $user = User::factory()->withPersonalCompany()->create();

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->call('leaveCompany');

    expect($user->currentTeam->fresh()->users)->toHaveCount(0);
});

test('company owners cant leave their own company', function () {
    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                    ->call('leaveCompany')
                    ->assertHasErrors(['company']);

    expect($user->currentTeam->fresh())->not->toBeNull();
});
