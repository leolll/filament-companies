<?php

use App\Models\User;
use Livewire\Livewire;
use Wallo\FilamentCompanies\Http\Livewire\UpdateTeamNameForm;

test('company names can be updated', function () {
    $this->actingAs($user = User::factory()->withPersonalCompany()->create());

    Livewire::test(UpdateTeamNameForm::class, ['company' => $user->currentTeam])
                ->set(['state' => ['name' => 'Test Company']])
                ->call('updateCompanyName');

    expect($user->fresh()->ownedCompanies)->toHaveCount(1);
    expect($user->currentTeam->fresh()->name)->toEqual('Test Company');
});
