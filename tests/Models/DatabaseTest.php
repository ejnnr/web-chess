<?php

class DatabaseTest extends TestCase
{
	public function setUp() {
		parent::setUp();
		Artisan::call('migrate');
		$this->seed('TestingSeeder');
	}

	public function testCreateDatabase()
	{
		$database = new App\Entities\Database();
		$database->name = 'test database';
		$database->ownerId = App\Entities\User::first()->id;
		$this->assertInstanceOf('App\Entities\Database', $database);
	}

	public function testSaveDatabase()
	{
		App\Entities\Database::create(['name' => 'created_database1', 'owner_id' => App\Entities\User::first()->id, 'public' => 0]);
		$this->assertEquals(1, App\Entities\Database::where('name', '=', 'created_database1')->count());
	}

	public function testGetDatabaseOwner()
	{
		$user4id = App\Entities\User::where('name', '=', 'user4')->first()->id;
		$database = App\Entities\Database::where('name', '=', 'private_database1')->first();
		$this->assertEquals($user4id, $database->owner->id);
	}

	public function testGetDatabaseGames()
	{
		$database_id = App\Entities\Database::where('name', '=', 'private_database1')->first()->id;
		$this->assertSame(1, App\Entities\Database::find($database_id)->games()->count());
	}

	public function testShareDatabase()
	{
		$user4id = App\Entities\User::where('name', '=', 'user4')->first()->id;
		$database = App\Entities\Database::where('name', '=', 'private_database1')->first();
		$database->share($user4id, 2);
		$this->assertEquals(2, $database->sharedWith->first()->pivot->access_level);
	}
}
