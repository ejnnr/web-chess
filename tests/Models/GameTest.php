<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GameTest extends TestCase
{
	public function setUp() {
		parent::setUp();
		Artisan::call('migrate');
		$this->seed('TestingSeeder');
	}

	public function testCreateGame()
	{
		$game = new App\Game();
		$this->assertInstanceOf(App\Game::class, $game);
	}

	public function testSaveGame()
	{
		$count = App\Game::all()->count();
		$game = new App\Game();
		$game->database_id = App\Database::all()->first()->id;
		$game->bcf = 'Some dummy data';
		$game->save();

		$this->assertSame(($count + 1), App\Game::all()->count());
	}

	public function testGetDatabase()
	{
		$game = App\Game::where('database_id', '=', 1)->first();
		$this->assertEquals(1, $game->database->id);
	}

	public function testShareGame()
	{
		$count = App\User::first()->sharedGames->count();
		$count2 = App\Game::first()->sharedWith->count();
		App\Game::first()->share(App\User::first()->id, 2);
		$this->assertSame($count + 1, App\User::first()->sharedGames->count());
		$this->assertSame($count + 1, App\Game::first()->sharedWith->count());
	}
}
