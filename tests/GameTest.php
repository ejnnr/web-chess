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

	public function testDoMove()
	{
		$game = new Game();
		$game->doMove($move1 = new Move('c2', 'c4'));
		$this->assertEquals(1, $game->moveCount());
		$this->assertEquals(new Position('rnbqkbnr/pppppppp/8/8/2P5/8/PP1PPPPP/RNBQKBNR b KQkq c3 0 1'), $game->getPosition());
		$this->assertEquals([$move1], $game->getMainline());

		$game->doMove($move2 = new Move('g8', 'f6'));
		$this->assertEquals(2, $game->moveCount());
		$this->assertEquals(new Position('rnbqkb1r/pppppppp/5n2/8/2P5/8/PP1PPPPP/RNBQKBNR w KQkq - 1 2'), $game->getPosition());
		$this->assertEquals([$move1, $move2], $game->getMainline());

		$game->doMove($move3 = new Move('d2', 'd4'));
		$this->assertEquals(3, $game->moveCount());
		$this->assertEquals(new Position('rnbqkb1r/pppppppp/5n2/8/2PP4/8/PP2PPPP/RNBQKBNR b KQkq d3 0 2'), $game->getPosition());
		$this->assertEquals([$move1, $move2, $move3], $game->getMainline());
	}

	public function testBack()
	{
		$game = new Game();
		$game->doMove(new Move('e2', 'e4'));
		$game->back();
		$this->assertEquals(new Position(), $game->getPosition());
	}
}
