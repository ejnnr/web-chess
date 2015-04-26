<?php

class UserTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->seed('TestingSeeder');
	}

	public function testGetUser() {
		$user = \App\User::where('name', '=', 'user1')->first();
		$this->assertEquals('user1@example.com', $user->email);
	}
}
