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
		$this->assertEquals(new Position('rnbqkbnr/pppppppp/8/8/2P5/8/PP1PPPPP/RNBQKBNR b KQkq c3 0 1'), $game->getPosition());

		$game->doMove($move2 = new Move('g8', 'f6'));
		$this->assertEquals(new Position('rnbqkb1r/pppppppp/5n2/8/2P5/8/PP1PPPPP/RNBQKBNR w KQkq - 1 2'), $game->getPosition());

		$game->doMove($move3 = new Move('d2', 'd4'));
		$this->assertEquals(new Position('rnbqkb1r/pppppppp/5n2/8/2PP4/8/PP2PPPP/RNBQKBNR b KQkq d3 0 2'), $game->getPosition());
	}

	public function testBack()
	{
		$game = new Game();
		$game->doMove(new Move('e2', 'e4'));
		$game->back();
		$this->assertEquals(new Position(), $game->getPosition());
	}

	public function testAddVariation()
	{
		$game = new Game();
		$game->doMove(new Move('e2', 'e4'));
		$game->doMove(new Move('e7', 'e5'));
		$game->addVariation(new Move('c7', 'c5'));
		$this->assertEquals(new Position('rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq c6 0 2'), $game->getPosition());
		$game->back();
		$this->assertEquals(new Position('rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'), $game->getPosition());
	}

	public function testEndVariation()
	{
		$game = new Game();
		$game->doMove(new Move('e2', 'e4'));
		$game->doMove(new Move('e7', 'e5'));
		$game->addVariation(new Move('c7', 'c5'));
		$game->endVariation();
		$this->assertEquals(new Position('rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6 0 2'), $game->getPosition());
	}
}
