<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('UserTableSeeder');
        $this->call('TagTableSeeder');
        $this->call('SharedTagsPivotTableSeeder');
        $this->call('GameTableSeeder');
        $this->call('GameTagTableSeeder');
    }
}

class UserTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('users')->delete();

        factory(App\Entities\User::class, 4)->create();

        App\Entities\User::create(['name' => 'root', 'email' => 'root@root.org', 'password' => 'root']);
    }
}

class TagTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('tags')->delete();

        factory(App\Entities\Tag::class, 4)->create();
    }
}

class SharedTagsPivotTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('shared_tags')->delete();

        $tag = \App\Entities\Tag::first();
        $tag->share(App\Entities\User::first()->id, 3);
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

class GameTagTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('game_tag')->delete();

        $ids = App\Entities\Tag::all(['id'])->modelKeys();
        foreach (App\Entities\Game::all()->all() as $game) {
            $game->tags()->attach($ids[mt_rand(0, count($ids) - 1)]);
        }
    }
}
