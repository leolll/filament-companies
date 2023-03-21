<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Wallo\FilamentCompanies\Http\Livewire\DeleteTeamForm;

class DeleteCompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_companies_can_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        $user->ownedCompanies()->save($company = Company::factory()->make([
            'personal_team' => false,
        ]));

        $company->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'test-role']
        );

        $component = Livewire::test(DeleteTeamForm::class, ['company' => $company->fresh()])
                                ->call('deleteCompany');

        $this->assertNull($company->fresh());
        $this->assertCount(0, $otherUser->fresh()->companies);
    }

    public function test_personal_companies_cant_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        $component = Livewire::test(DeleteTeamForm::class, ['company' => $user->currentTeam])
                                ->call('deleteCompany')
                                ->assertHasErrors(['company']);

        $this->assertNotNull($user->currentTeam->fresh());
    }
}
