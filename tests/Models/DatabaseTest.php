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
		$database = new App\Database();
		$database->name = 'test database';
		$database->ownerId = App\User::first()->id;
		$this->assertInstanceOf('App\Database', $database);
	}

	public function testSaveDatabase()
	{
		App\Database::create(['name' => 'created_database1', 'owner_id' => App\User::first()->id, 'public' => 0]);
		$this->assertEquals(1, App\Database::where('name', '=', 'created_database1')->count());
	}

	public function testGetDatabaseOwner()
	{
		$user4id = App\User::where('name', '=', 'user4')->first()->id;
		$database = App\Database::where('name', '=', 'private_database1')->first();
		$this->assertEquals($user4id, $database->owner->id);
	}

	public function testShareDatabase()
	{
		$user4id = App\User::where('name', '=', 'user4')->first()->id;
		$database = App\Database::where('name', '=', 'private_database1')->first();
		$database->share($user4id, 2);
		$this->assertEquals(2, $database->sharedWith->first()->pivot->access_level);
	}
}
