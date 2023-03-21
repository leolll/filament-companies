<?php

use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;
use Wallo\FilamentCompanies\Http\Livewire\DeleteTeamForm;

test('companies can be deleted', function () {
    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    $user->ownedCompanies()->save($company = Company::factory()->make([
        'personal_team' => false,
    ]));

    $company->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'test-role']
    );

    $component = Livewire::test(DeleteTeamForm::class, ['company' => $company->fresh()])
                            ->call('deleteCompany');

    expect($company->fresh())->toBeNull();
    expect($otherUser->fresh()->companies)->toHaveCount(0);
});

test('personal companies cant be deleted', function () {
    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    $component = Livewire::test(DeleteTeamForm::class, ['company' => $user->currentTeam])
                            ->call('deleteCompany')
                            ->assertHasErrors(['company']);

    expect($user->currentTeam->fresh())->not->toBeNull();
});
