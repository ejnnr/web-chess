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

	/**
 	 * @expectedException     GameException
 	 * @expectedExceptionCode 140
 	 */
	public function testDoIllegalMove()
	{
		$game = new Game();
		$game->doMove(new Move('c2', 'c5'));
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
		$game->doMove(new Move('g1', 'f3'));
		$game->doMove(new Move('d7', 'd6'));
		$game->addVariation(new Move('b8', 'c6'));
		$game->endVariation();
		$this->assertEquals(new Position('rnbqkbnr/pp2pppp/3p4/2p5/4P3/5N2/PPPP1PPP/RNBQKB1R w KQkq - 0 3'), $game->getPosition());
		$game->doMove(new Move('d2', 'd4'));
		$game->endVariation();
		$this->assertEquals(new Position('rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6 0 2'), $game->getPosition());
		$game->addVariation(new Move('e7', 'e6'));
		$game->endVariation();
		$this->assertEquals(new Position('rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6 0 2'), $game->getPosition());
	}

	public function testHeaders()
	{
		$game = new Game();
		$game->setHeader('turn', 3);
		$this->assertEquals(3, $game->getHeader('turn'));
		$this->assertEquals(['turn' => 3], $game->getHeaders());
		$game->setHeaders(['turn' => 2, 'site' => 'Berlin']);
		$this->assertEquals(['turn' => 2, 'site' => 'Berlin'], $game->getHeaders());
	}

	/**
 	 * @expectedException     GameException
 	 * @expectedExceptionCode 4
 	 */
	public function testSetInvalidHeader()
	{
		$game = new Game();
		$game->setHeader(3, 23);
	}
}
