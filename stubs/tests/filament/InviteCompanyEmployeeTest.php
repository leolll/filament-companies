<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;
use Wallo\FilamentCompanies\Features;
use Wallo\FilamentCompanies\Http\Livewire\TeamMemberManager;
use Wallo\FilamentCompanies\Mail\CompanyInvitation;

class InviteCompanyEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_employees_can_be_invited_to_company(): void
    {
        if (! Features::sendsCompanyInvitations()) {
            $this->markTestSkipped('Company invitations not enabled.');

            return;
        }

        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        $component = Livewire::test(TeamMemberManager::class, ['company' => $user->currentTeam])
                        ->set('addTeamMemberForm', [
                            'email' => 'test@example.com',
                            'role' => 'admin',
                        ])->call('addTeamMember');

        Mail::assertSent(CompanyInvitation::class);

        $this->assertCount(1, $user->currentTeam->fresh()->companyInvitations);
    }

    public function test_company_employee_invitations_can_be_cancelled(): void
    {
        if (! Features::sendsCompanyInvitations()) {
            $this->markTestSkipped('Company invitations not enabled.');

            return;
        }

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

        $this->assertCount(0, $user->currentTeam->fresh()->companyInvitations);
    }
}
