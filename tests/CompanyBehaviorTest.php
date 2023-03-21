<?php

namespace Wallo\FilamentCompanies\Tests;

use App\Actions\FilamentCompanies\CreateCompany;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\TransientToken;
use Wallo\FilamentCompanies\Company;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Tests\Fixtures\CompanyPolicy;
use Wallo\FilamentCompanies\Tests\Fixtures\User;

class CompanyBehaviorTest extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Gate::policy(\App\Models\Company::class, CompanyPolicy::class);
        FilamentCompanies::useUserModel(User::class);
    }

    public function test_company_relationship_methods()
    {
        $this->migrate();

        $action = new CreateCompany;

        $user = User::forceCreate([
            'name' => 'Andrew Wallo',
            'email' => 'andrewdwallo@gmail.com',
            'password' => 'secret',
        ]);

        $company = $action->create($user, ['name' => 'Test Company']);

        $this->assertInstanceOf(Company::class, $company);

        $this->assertTrue($user->belongsTeam($company));
        $this->assertTrue($user->ownsTeam($company));
        $this->assertCount(1, $user->fresh()->ownedCompanies);
        $this->assertCount(1, $user->fresh()->allTeams());

        $company->forceFill(['personal_team' => true])->save();

        $this->assertEquals($company->id, $user->fresh()->personalTeam()->id);
        $this->assertEquals($company->id, $user->fresh()->currentTeam->id);
        $this->assertTrue($user->hasCompanyPermission($company, 'foo'));

        // Test with another user that isn't on the company...
        $otherUser = User::forceCreate([
            'name' => 'Dan Harrin',
            'email' => 'danharrin@filament.com',
            'password' => 'secret',
        ]);

        $this->assertFalse($otherUser->belongsTeam($company));
        $this->assertFalse($otherUser->ownsTeam($company));
        $this->assertFalse($otherUser->hasCompanyPermission($company, 'foo'));

        // Add the other user to the company...
        FilamentCompanies::role('editor', 'Editor', ['foo']);

        $otherUser->companies()->attach($company, ['role' => 'editor']);
        $otherUser = $otherUser->fresh();

        $this->assertTrue($otherUser->belongsTeam($company));
        $this->assertFalse($otherUser->ownsTeam($company));

        $this->assertTrue($otherUser->hasCompanyPermission($company, 'foo'));
        $this->assertFalse($otherUser->hasCompanyPermission($company, 'bar'));

        $this->assertTrue($company->userHasPermission($otherUser, 'foo'));
        $this->assertFalse($company->userHasPermission($otherUser, 'bar'));

        $otherUser->withAccessToken(new TransientToken);

        $this->assertTrue($otherUser->belongsTeam($company));
        $this->assertFalse($otherUser->ownsTeam($company));

        $this->assertTrue($otherUser->hasCompanyPermission($company, 'foo'));
        $this->assertFalse($otherUser->hasCompanyPermission($company, 'bar'));

        $this->assertTrue($company->userHasPermission($otherUser, 'foo'));
        $this->assertFalse($company->userHasPermission($otherUser, 'bar'));
    }

    public function test_has_company_permission_checks_token_permissions()
    {
        FilamentCompanies::role('admin', 'Administrator', ['foo']);

        $this->migrate();

        $action = new CreateCompany;

        $user = User::forceCreate([
            'name' => 'Andrew Wallo',
            'email' => 'andrewdwallo@gmail.com',
            'password' => 'secret',
        ]);

        $company = $action->create($user, ['name' => 'Test Company']);

        $dan = User::forceCreate([
            'name' => 'Dan Harrin',
            'email' => 'danharrin@filament.com',
            'password' => 'secret',
        ]);

        $authToken = new Sanctum;
        $dan = $authToken->actingAs($dan, ['bar'], []);

        $company->users()->attach($dan, ['role' => 'admin']);

        $this->assertFalse($dan->hasCompanyPermission($company, 'foo'));

        $john = User::forceCreate([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'secret',
        ]);

        $authToken = new Sanctum;
        $john = $authToken->actingAs($john, ['foo'], []);

        $company->users()->attach($john, ['role' => 'admin']);

        $this->assertTrue($john->hasCompanyPermission($company, 'foo'));
    }

    public function test_user_does_not_need_to_refresh_after_switching_companies()
    {
        $this->migrate();

        $action = new CreateCompany;

        $user = User::forceCreate([
            'name' => 'Andrew Wallo',
            'email' => 'andrewdwallo@gmail.com',
            'password' => 'secret',
        ]);

        $personalCompany = $action->create($user, ['name' => 'Personal Company']);

        $personalCompany->forceFill(['personal_team' => true])->save();

        $this->assertTrue($user->isCurrentTeam($personalCompany));

        $anotherCompany = $action->create($user, ['name' => 'Test Company']);

        $this->assertTrue($user->isCurrentTeam($anotherCompany));
    }

    protected function migrate()
    {
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }
}
