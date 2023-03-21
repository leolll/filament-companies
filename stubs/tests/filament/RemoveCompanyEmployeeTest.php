<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;

class RemoveCompanyEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_employees_can_be_removed_from_companies(): void
    {
        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                        ->set('companyEmployeeIdBeingRemoved', $otherUser->id)
                        ->call('removeCompanyEmployee');

        $this->assertCount(0, $user->currentTeam->fresh()->users);
    }

    public function test_only_company_owner_can_remove_company_employees(): void
    {
        $user = User::factory()->withPersonalCompany()->create();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                        ->set('companyEmployeeIdBeingRemoved', $user->id)
                        ->call('removeCompanyEmployee')
                        ->assertStatus(403);
    }
}
