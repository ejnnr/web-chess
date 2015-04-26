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

		\App\Database::create(['name' => 'private_database1', 'owner_id' => 4, 'public' => FALSE]);
        \App\Database::create(['name' => 'shared_database1', 'owner_id' => 1, 'public' => FALSE]);
        \App\Database::create(['name' => 'shared_database2', 'owner_id' => 2, 'public' => FALSE]);
        \App\Database::create(['name' => 'public_database1', 'owner_id' => 3, 'public' => TRUE]);
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
