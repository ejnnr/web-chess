<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TestingSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('TestingUserTableSeeder');
		$this->call('TestingDatabaseTableSeeder');
		$this->call('TestingGameTableSeeder');
		$this->call('TestingSharedDatabasesPivotTableSeeder');
	}

}

class TestingUserTableSeeder extends Seeder {

    public function run()
    {
        \DB::table('users')->delete();

        \App\User::create(['name' => 'user1', 'email' => 'user1@example.com', 'password' => 'password']);
        \App\User::create(['name' => 'user2', 'email' => 'user2@example.com', 'password' => 'password']);
        \App\User::create(['name' => 'user3', 'email' => 'user3@example.com', 'password' => 'password']);
        \App\User::create(['name' => 'user4', 'email' => 'user4@example.com', 'password' => 'password']);
    }

}

class TestingDatabaseTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('databases')->delete();

		$user1 = App\User::where('name', '=', 'user1')->first()->id;
		$user2 = App\User::where('name', '=', 'user2')->first()->id;
		$user3 = App\User::where('name', '=', 'user3')->first()->id;
		$user4 = App\User::where('name', '=', 'user4')->first()->id;

		\App\Database::create(['name' => 'private_database1', 'owner_id' => $user4, 'public' => FALSE]);
        \App\Database::create(['name' => 'shared_database1', 'owner_id' => $user1, 'public' => FALSE]);
        \App\Database::create(['name' => 'shared_database2', 'owner_id' => $user2, 'public' => FALSE]);
        \App\Database::create(['name' => 'public_database1', 'owner_id' => $user3, 'public' => TRUE]);
	}
}

class TestingSharedDatabasesPivotTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('shared_databases')->delete();

		$database = \App\Database::where('name', '=', 'shared_database1')->first();
		$id = \App\User::where('name', '=', 'user2')->first()->id;
		$database->share($id, 3);

		$database = \App\Database::where('name', '=', 'shared_database2')->first();
		$id = \App\User::where('name', '=', 'user4')->first()->id;
		$database->share($id, 2);
	}
}

class TestingGameTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('games')->delete();

		$database1 = App\Database::where('name', '=', 'private_database1')->first()->id;
		$database2 = App\Database::where('name', '=', 'shared_database1')->first()->id;
		$database3 = App\Database::where('name', '=', 'shared_database2')->first()->id;
		$database4 = App\Database::where('name', '=', 'public_database1')->first()->id;

		\App\Game::create(['database_id' => $database1, 'bcf' => 'dummy text']);
        \App\Game::create(['database_id' => $database2, 'bcf' => 'dummy text']);
        \App\Game::create(['database_id' => $database3, 'bcf' => 'dummy text']);
        \App\Game::create(['database_id' => $database4, 'bcf' => 'dummy text']);
	}
}
