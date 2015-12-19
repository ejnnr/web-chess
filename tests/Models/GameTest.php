<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GameTest extends TestCase
{
	public function setUp() {
		parent::setUp();
		Artisan::call('migrate');
		$this->seed('DatabaseSeeder');
	}

	public function testCreateGame()
	{
		$game = new App\Entities\Game();
		$this->assertInstanceOf(App\Entities\Game::class, $game);
	}

	public function testSaveGame()
	{
		$count = App\Entities\Game::all()->count();
		$game = new App\Entities\Game();
		$game->bcf = 'Some dummy data';
		$game->owner_id = App\Entities\User::first()->id;
		$game->save();

		$this->assertSame(($count + 1), App\Entities\Game::all()->count());
	}

	public function testGetTags()
	{
		$game = App\Entities\Game::first();
		$this->assertInstanceOf(App\Entities\Tag::class, $game->tags()->first());
	}

	public function testShareGame()
	{
		$count = App\Entities\User::first()->sharedGames->count();
		$count2 = App\Entities\Game::first()->sharedWith->count();
		App\Entities\Game::first()->share(App\Entities\User::first()->id, 2);
		$this->assertSame($count + 1, App\Entities\User::first()->sharedGames->count());
		$this->assertSame($count + 1, App\Entities\Game::first()->sharedWith->count());
	}

	public function testGameAttribute()
	{
		$model = new App\Entities\Game();
		$this->assertInstanceOf(App\Chess\BCFGame::class, $model->game);
		$game = new App\Chess\BCFGame();
		$game->doMove(new App\Chess\Move('b2', 'b3'));
		$model->game = $game;
		$this->assertEquals($game, $model->game);
		$this->assertEquals($game->getBCF(), $model->bcf);
	}
}
