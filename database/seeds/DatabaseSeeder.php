<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('UserTableSeeder');
		$this->call('DatabaseTableSeeder');
		$this->call('SharedDatabasesPivotTableSeeder');
	}

}

class UserTableSeeder extends Seeder {

    public function run()
    {
        \DB::table('users')->delete();

        \App\User::create(['name' => 'user1', 'email' => 'foo@bar.com', 'password' => 'password']);
        \App\User::create(['name' => 'user2', 'email' => 'test@bar.com', 'password' => '123456']);
        \App\User::create(['name' => 'user3', 'email' => 'user3@bar.com', 'password' => 'test']);
        \App\User::create(['name' => 'user4', 'email' => 'user4@bar.com', 'password' => 'user4']);
    }

}

class DatabaseTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('databases')->delete();

        \App\Database::create(['name' => 'sample database', 'owner_id' => 1, 'public' => FALSE]);
        \App\Database::create(['name' => 'my games', 'owner_id' => 2, 'public' => FALSE]);
        \App\Database::create(['name' => 'grandmaster games', 'owner_id' => 3, 'public' => TRUE]);
	}
}

class SharedDatabasesPivotTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('shared_databases')->delete();

		$database = \App\Database::where('name', '=', 'sample database')->first();
		$id = \App\User::where('name', '=', 'user2')->first()->id;
		$database->share($id, 3);

		$database = \App\Database::where('name', '=', 'my games')->first();
		$id = \App\User::where('name', '=', 'user4')->first()->id;
		$database->share($id, 2);
	}
}
