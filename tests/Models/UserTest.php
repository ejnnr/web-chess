<?php

class UserTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		\Artisan::call('migrate');
		$this->seed('TestingSeeder');
	}

	public function testCreateUser() {
		$user = new App\Entities\User();
		$user->name = 'createdUser1';
		$user->email = 'createdUser1@example.com';
		$user->password = 'password';
		$this->assertInstanceOf('App\Entities\User', $user);
	}

	public function testSaveUser() {
		App\Entities\User::create(['name' => 'savedUser1', 'email' => 'savedUser1@example.com', 'password' => 'password']);
		$user = App\Entities\User::where('name', '=', 'savedUser1')->first();
		$this->assertEquals('savedUser1@example.com', $user->email);
	}

	public function testGetUser() {
		$user = \App\Entities\User::where('name', '=', 'user1')->first();
		$this->assertEquals('user1@example.com', $user->email);
	}

	public function testPasswordIsHashed() {
		$user = new App\Entities\User();
		$user->password = 'password';
		$this->assertTrue(Hash::check('password', $user->password));
	}

	public function testGetDatabases() {
		$user = App\Entities\User::where('name', '=', 'user4')->first();
		$this->assertEquals(1, $user->databases()->count());
	}

	public function testGetGames() {
		$count = App\Entities\User::first()->games()->count();
		App\Entities\User::first()->databases()->first()->games()->save(new App\Entities\Game(['bcf' => 'hello world']));
		$this->assertSame($count + 1, App\Entities\User::first()->games()->count());
	}
}
