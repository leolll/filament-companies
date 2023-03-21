<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;

class UpdateCompanyEmployeeRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_employee_roles_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                        ->set('managingRoleFor', $otherUser)
                        ->set('currentRole', 'editor')
                        ->call('updateRole');

        $this->assertTrue($otherUser->fresh()->hasCompanyRole(
            $user->currentTeam->fresh(), 'editor'
        ));
    }

    public function test_only_company_owner_can_update_company_employee_roles(): void
    {
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

        $this->assertTrue($otherUser->fresh()->hasCompanyRole(
            $user->currentTeam->fresh(), 'admin'
        ));
    }
}
