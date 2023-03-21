<?php

namespace Wallo\FilamentCompanies\Tests;

use App\Actions\FilamentCompanies\CreateCompany;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Tests\Fixtures\CompanyPolicy;
use Wallo\FilamentCompanies\Tests\Fixtures\User;

class CurrentCompanyControllerTest extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Gate::policy(Company::class, CompanyPolicy::class);
        FilamentCompanies::useUserModel(User::class);
    }

    public function test_can_switch_to_company_the_user_belongs_to()
    {
        $this->migrate();

        $action = new CreateCompany;

        $user = User::forceCreate([
            'name' => 'Andrew Wallo',
            'email' => 'andrewdwallo@gmail.com',
            'password' => 'secret',
        ]);

        $company = $action->create($user, ['name' => 'Test Company']);

        $response = $this->actingAs($user)->put('/current-company', ['company_id' => $company->id]);

        $response->assertRedirect();

        $this->assertEquals($company->id, $user->fresh()->currentTeam->id);
        $this->assertTrue($user->isCurrentTeam($company));
    }

    public function test_cant_switch_to_company_the_user_does_not_belong_to()
    {
        $this->migrate();

        $action = new CreateCompany;

        $user = User::forceCreate([
            'name' => 'Andrew Wallo',
            'email' => 'andrewdwallo@gmail.com',
            'password' => 'secret',
        ]);

        $company = $action->create($user, ['name' => 'Test Company']);

        $otherUser = User::forceCreate([
            'name' => 'Dan Harrin',
            'email' => 'danharrin@filament.com',
            'password' => 'secret',
        ]);

        $response = $this->actingAs($otherUser)->put('/current-company', ['company_id' => $company->id]);

        $response->assertStatus(403);
    }

    protected function migrate()
    {
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('filament-companies.stack', 'filament');
        $app['config']->set('filament-companies.features', ['companies']);
    }
}
