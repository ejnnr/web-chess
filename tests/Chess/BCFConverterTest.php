<?php namespace App\Chess;

class BCFConverterTest extends \TestCase
{
	public function testCanCreateBCFConverter()
	{
		$conv = new BCFConverter();
		$this->assertInstanceOf(BCFConverter::class, $conv);
	}

	public function testEncodeMove()
	{
		$conv = new BCFConverter();
		$this->assertSame('10000000111111', decbin(hexdec($conv->encodeMove(new Move(32, 63), false))));
		$this->assertSame('110000010111111', decbin(hexdec($conv->encodeMove(new Move(32, 63, PROMOTION_BISHOP), true))));
		$this->assertSame('100000001111111000000100000011', decbin(hexdec($conv->encodeMove(new Move(32, 63, PROMOTION_QUEEN, [3]), false))));
	}
}
