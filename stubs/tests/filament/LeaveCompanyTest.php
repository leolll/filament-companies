<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;

class LeaveCompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_leave_companies(): void
    {
        $user = User::factory()->withPersonalCompany()->create();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                        ->call('leaveCompany');

        $this->assertCount(0, $user->currentTeam->fresh()->users);
    }

    public function test_company_owners_cant_leave_their_own_company(): void
    {
        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                        ->call('leaveCompany')
                        ->assertHasErrors(['company']);

        $this->assertNotNull($user->currentTeam->fresh());
    }
}
