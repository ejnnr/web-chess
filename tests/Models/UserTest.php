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
		$user = new App\User();
		$user->name = 'createdUser1';
		$user->email = 'createdUser1@example.com';
		$user->password = 'password';
		$this->assertInstanceOf('App\User', $user);
	}

	public function testSaveUser() {
		App\User::create(['name' => 'savedUser1', 'email' => 'savedUser1@example.com', 'password' => 'password']);
		$user = App\User::where('name', '=', 'savedUser1')->first();
		$this->assertEquals('savedUser1@example.com', $user->email);
	}

	public function testGetUser() {
		$user = \App\User::where('name', '=', 'user1')->first();
		$this->assertEquals('user1@example.com', $user->email);
	}

	public function testPasswordIsHashed() {
		$user = new App\User();
		$user->password = 'password';
		$this->assertTrue(Hash::check('password', $user->password));
	}

	public function testGetDatabases() {
		$user = App\User::where('name', '=', 'user4')->first();
		$this->assertEquals(1, $user->databases()->count());
	}
}
