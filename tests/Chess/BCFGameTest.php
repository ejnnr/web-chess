<?php namespace App\Chess;

class BCFGameTest extends \TestCase
{
	public function testCanCreateBCFGame()
	{
		$game = new BCFGame();
		$this->assertInstanceOf(BCFGame::class, $game);
	}

	public function testEncodeMove()
	{
		$game = new BCFGame();
		$this->assertSame('10000000111111', decbin(hexdec($game->encodeMove(new Move(32, 63), false))));
		$this->assertSame('110000010111111', decbin(hexdec($game->encodeMove(new Move(32, 63, PROMOTION_BISHOP), true))));
		$this->assertSame('100000001111111000000100000011', decbin(hexdec($game->encodeMove(new Move(32, 63, PROMOTION_QUEEN, [3]), false))));
	}
}
