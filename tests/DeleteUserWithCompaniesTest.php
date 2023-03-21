<?php

namespace Wallo\FilamentCompanies\Tests;

use App\Actions\FilamentCompanies\CreateCompany;
use App\Actions\FilamentCompanies\DeleteCompany;
use App\Actions\FilamentCompanies\DeleteUser;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Tests\Fixtures\CompanyPolicy;
use Wallo\FilamentCompanies\Tests\Fixtures\User;

class DeleteUserWithCompaniesTest extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Gate::policy(Company::class, CompanyPolicy::class);
        FilamentCompanies::useUserModel(User::class);
    }

    public function test_user_can_be_deleted()
    {
        $this->migrate();

        $company = $this->createCompany();
        $otherCompany = $this->createCompany();

        $otherCompany->users()->attach($company->owner, ['role' => null]);

        $this->assertSame(2, DB::table('companies')->count());
        $this->assertSame(1, DB::table('company_user')->count());

        copy(__DIR__.'/../stubs/app/Actions/FilamentCompanies/DeleteUserWithCompanies.php', $fixture = __DIR__.'/Fixtures/DeleteUser.php');

        require $fixture;

        $action = new DeleteUser(new DeleteCompany);

        $action->delete($company->owner);

        $this->assertNull($company->owner->fresh());
        $this->assertSame(1, DB::table('companies')->count());
        $this->assertSame(0, DB::table('company_user')->count());

        @unlink($fixture);
    }

    protected function createCompany()
    {
        $action = new CreateCompany;

        $user = User::forceCreate([
            'name' => Str::random(10),
            'email' => Str::random(10).'@filament.com',
            'password' => 'secret',
        ]);

        return $action->create($user, ['name' => 'Test Company']);
    }

    protected function migrate()
    {
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        Schema::create('personal_access_tokens', function ($table) {
            $table->id();
            $table->foreignId('tokenable_id');
            $table->string('tokenable_type');
        });
    }
}
