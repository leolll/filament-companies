<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Wallo\FilamentCompanies\Http\Livewire\UpdateTeamNameForm;

class UpdateCompanyNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_names_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalCompany()->create());

        Livewire::test(UpdateTeamNameForm::class, ['company' => $user->currentTeam])
                    ->set(['state' => ['name' => 'Test Company']])
                    ->call('updateCompanyName');

        $this->assertCount(1, $user->fresh()->ownedCompanies);
        $this->assertEquals('Test Company', $user->currentTeam->fresh()->name);
    }
}
