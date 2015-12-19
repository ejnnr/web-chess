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
		$this->call('GameTableSeeder');
	}

}

class UserTableSeeder extends Seeder {

    public function run()
    {
        \DB::table('users')->delete();

		factory(App\Entities\User::class, 4)->create();
    }

}

class DatabaseTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('databases')->delete();

		factory(App\Entities\Database::class, 4)->create();
	}
}

class SharedDatabasesPivotTableSeeder extends Seeder {
	public function run()
	{
        \DB::table('shared_databases')->delete();

		$database = \App\Database::first();
		$database->share(App\Entities\User::orderByRaw("RAND()")->first()->id, 3);
	}
}

class GameTableSeeder extends Seeder
{
	public function run()
	{
        \DB::table('games')->delete();

		factory(App\Entities\Game::class, 4)->create();
	}
}
