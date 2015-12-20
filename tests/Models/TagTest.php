<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TagTest extends TestCase
{
	use DatabaseTransactions;

	public function testCreateTag()
	{
		$tag = new App\Entities\Tag();
		$this->assertInstanceOf('App\Entities\Tag', $tag);
	}

	public function testSaveTag()
	{
		App\Entities\Tag::create(['name' => 'created_tag', 'owner_id' => App\Entities\User::first()->id, 'public' => 0]);
		$this->assertEquals(1, App\Entities\Tag::where('name', '=', 'created_tag')->count());
	}

	public function testGetTagOwner()
	{
		$this->assertInstanceOf(App\Entities\User::class, App\Entities\Tag::first()->owner);
	}

	public function testGetTagGames()
	{
		$this->assertInstanceOf(App\Entities\Game::class, App\Entities\Game::first()->tags()->first()->games()->first());
	}

	public function testShareTag()
	{
		$ids = App\Entities\User::all(['id'])->modelKeys();
		$tag = App\Entities\Tag::create(['name' => 'shared_tag', 'public' => 0, 'owner_id' => App\Entities\User::first()->id]);
		$tag->share($ids[mt_rand(0, count($ids) - 1)], 2);
		$this->assertEquals(2, $tag->sharedWith->first()->pivot->access_level);
	}
}
