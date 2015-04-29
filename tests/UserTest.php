<?php

class UserTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		\Artisan::call('migrate');
		$this->seed('DatabaseSeeder');
	}

	public function testGetUser() {
		$user = \App\User::where('name', '=', 'user1')->first();
		$this->assertEquals('foo@bar.com', $user->email);
	}
}
