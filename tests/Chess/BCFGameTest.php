<?php namespace App\Chess;

class BCFGameTest extends \PHPUnit_Framework_TestCase
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

	public function testDecodeMove()
	{
		$game = new BCFGame();
		$move = $game->decodeMove('73bb810181048468656c6c6f20776f726c6400');
		$this->assertEquals(new Move('d7', 'd8', PROMOTION_BISHOP, [1, 4], 'hello world'), $move);
	}

	public function testBCFConverting()
	{
		$game1 = new BCFGame();
		$game2 = new BCFGame();

		$game1->doMove(new Move('d2', 'd4'));
		$game2->loadBCF($game1->getBCF());
		$this->assertEquals($game1, $game2);

		$game1->doMove(new Move('g8', 'f6'));
		$game2->loadBCF($game1->getBCF());
		$this->assertEquals($game1, $game2);

		$game1->doMove(new Move('c2', 'c4', PROMOTION_QUEEN, [1]));
		$game2->loadBCF($game1->getBCF());
		$this->assertEquals($game1, $game2);

		$game1->doMove(new Move('e7', 'e6', PROMOTION_QUEEN, [], 'This move is played the most often.'));
		$game2->loadBCF($game1->getBCF());
		$this->assertEquals($game1, $game2);

		$game1->addVariation(new Move('g7', 'g6'));
		$game1->doMove(new Move('g1', 'f3', PROMOTION_QUEEN, [], "White doesn't allow the Grunfeld yet."));
		$game1->endVariation();
		$game2->loadBCF($game1->getBCF());
		$this->assertEquals($game1, $game2);

		$game1->addVariation(new Move('c7', 'c5'));
		$game1->doMove(new Move('b1', 'c3'));
		$game1->endVariation();
		$game2->loadBCF($game1->getBCF());
		$this->assertEquals($game1, $game2);
	}
}
