<?php

require_once 'include/Game.php';

class GameTest extends PHPUnit_Framework_TestCase
{
	public function testCreateGame()
	{
		$game = new Game();
		$this->assertInstanceOf('Game', $game);
	}

	public function testGetPosition()
	{
		$game = new Game();
		$this->assertEquals(new Position(), $game->getPosition());
	}
}
