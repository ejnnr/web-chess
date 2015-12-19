<?php

class UserTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		\Artisan::call('migrate');
		$this->seed('DatabaseSeeder');
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

	public function testPasswordIsHashed() {
		$user = new App\Entities\User();
		$user->password = 'password';
		$this->assertTrue(Hash::check('password', $user->password));
	}

	public function testGetTags() {
		$user = App\Entities\User::first();
		$user->tags()->create(['name' => 'some random tag', 'owner_id' => $user->id, 'public' => 0]);
		$this->assertInstanceOf(App\Entities\Tag::class, $user->tags()->first());
	}
}
