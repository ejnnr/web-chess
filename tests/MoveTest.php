<?php namespace App\Chess;

class MoveTest extends \TestCase
{
	public function testCreateValidMove()
	{
		$this->assertInstanceOf('App\Chess\Move', new Move('a1', 'a2'));
		$move = new Move(0, 8, PROMOTION_QUEEN, array(16, 1));
		$this->assertInstanceOf('App\Chess\Move', $move);
		$this->assertInstanceOf('App\Chess\Move', new Move(array(0, 0), array(0, 1)));
		return $move;
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveWrongArgumentTypeDeparture()
	{
		$move = new Move(FALSE, 'd5');
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveWrongArgumentTypeDestination()
	{
		$move = new Move('e4', TRUE);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveWrongArgumentTypePromotion()
	{
		$move = new Move('a4', 'a5', 'a6');
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 2
	 */
	public function testCreateMoveNullArgument()
	{
		$move = new Move(NULL, 'e3');
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateMoveBadSquareValueIntegerDeparture()
	{
		$move = new Move(-5, 62);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateMoveBadSquareValueStringDeparture()
	{
		$move = new Move('c9', 'h4');
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateMoveBadSquareValueArrayDeparture()
	{
		$move = new Move([2,8], [7, -1]);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateMoveBadSquareValueIntegerDestination()
	{
		$move = new Move(34, 89);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateMoveBadSquareValueStringDestination()
	{
		$move = new Move('c3', 'j4');
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateMoveBadSquareValueArrayDestination()
	{
		$move = new Move([2,7], [7, -1]);
	}

	public function testCreateMoveEmptyPromotion()
	{
		$move = new Move('c7', 'c8', NULL);
		$this->assertSame(PROMOTION_QUEEN, $move->getPromotion());
	}

	public function testCreateMoveCharPromotion()
	{
		$move = new Move('a7', 'a8', 'Q');
		$this->assertSame(PROMOTION_QUEEN, $move->getPromotion());
		$move = new Move('a7', 'a8', 'R');
		$this->assertSame(PROMOTION_ROOK, $move->getPromotion());
		$move = new Move('a7', 'a8', 'B');
		$this->assertSame(PROMOTION_BISHOP, $move->getPromotion());
		$move = new Move('a7', 'a8', 'N');
		$this->assertSame(PROMOTION_KNIGHT, $move->getPromotion());
	}

	/**
 	 * @expectedException     App\Chess\MoveException
 	 * @expectedExceptionCode 7
 	 */
	public function testCreateMovePromotionOutOfRange()
	{
		$move = new Move('g2', 'g1', 5);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveNAGNoArray()
	{
		$move = new Move('e2', 'e3', PROMOTION_QUEEN, 'string');
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateDoubleNAG()
	{
		$move = new Move('e2', 'e3', PROMOTION_QUEEN, [1, 1, 3]);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveNAGNoInteger()
	{
		$move = new Move('e2', 'e3', PROMOTION_QUEEN, [2, 'test']);
	}

	/**
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 7
	 */
	public function testCreateNAGOutsideRange()
	{
		$move = new Move('e2', 'e3', PROMOTION_QUEEN, [1, 256, 3]);
	}

	/**
 	 * @expectedException     App\Chess\MoveException
 	 * @expectedExceptionCode 4
 	 */
	public function testCreateMoveCommentNoString()
	{
		$move = new Move('e3', 'd5', PROMOTION_QUEEN, [], 3);
	}

	/**
	 * @depends testCreateValidMove
	 */
	public function testGetDeparture(Move $move)
	{
		$this->assertSame(0, $move->getDeparture(SQUARE_FORMAT_INT));
		$this->assertSame('a1', $move->getDeparture(SQUARE_FORMAT_STRING));
		$this->assertSame(array(0, 0), $move->getDeparture(SQUARE_FORMAT_ARRAY));
	}

	/**
	 * @depends testCreateValidMove
	 */
	public function testGetDestination(Move $move)
	{
		$this->assertSame(8, $move->getDestination(SQUARE_FORMAT_INT));
		$this->assertSame('a2', $move->getDestination(SQUARE_FORMAT_STRING));
		$this->assertSame(array(0, 1), $move->getDestination(SQUARE_FORMAT_ARRAY));
	}

	/**
	 * @depends testCreateValidMove
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 7
	 */
	public function testGetDepartureInvalidFormat(Move $move)
	{
		$move->getDeparture(0.5);
	}

	/**
	 * @depends testCreateValidMove
	 * @expectedException     App\Chess\MoveException
	 * @expectedExceptionCode 7
	 */
	public function testGetDestinationInvalidFormat(Move $move)
	{
		$move->getDestination(0.5);
	}
	
	/**
	 * @depends testCreateValidMove
	 */
	public function testGetPromotion(Move $move)
	{
		$this->assertSame(PROMOTION_QUEEN, $move->getPromotion());
	}

	/**
	 * @depends testCreateValidMove
	 */
	public function testGetNAGs(Move $move)
	{
		$this->assertSame(array(1, 16), $move->getNAGs());
	}
}
?>
