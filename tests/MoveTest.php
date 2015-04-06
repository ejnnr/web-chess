<?php
require_once 'include/Move.php';

class MoveTest extends PHPUnit_Framework_TestCase
{
	public function testCreateValidMove()
	{
		$this->assertInstanceOf('Move', new Move('a1', 'a2'));
		$move = new Move(0, 8, PROMOTION_QUEEN, array(16, 1));
		$this->assertInstanceOf('Move', $move);
		$this->assertInstanceOf('Move', new Move(array(0, 0), array(0, 1)));
		return $move;
	}

	/**
	 * @expectedException     MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveWrongArgumentType()
	{
		$move = new Move(FALSE, TRUE);
	}

	/**
	 * @expectedException     MoveException
	 * @expectedExceptionCode 4
	 */
	public function testCreateMoveWrongArgumentTypePromotion()
	{
		$move = new Move('a4', 'a5', 'a6');
	}

	/**
	 * @expectedException     MoveException
	 * @expectedExceptionCode 2
	 */
	public function testCreateMoveNullArgument()
	{
		$move = new Move(NULL, 'e3');
	}

	/**
	 * @expectedException     MoveException
	 * @expectedExceptionCode 5
	 */
	public function testCreateDoubleNAG()
	{
		$move = new Move('e2', 'e3', PROMOTION_QUEEN, [1, 1, 3]);
	}

	/**
	 * @expectedException     MoveException
	 * @expectedExceptionCode 7
	 */
	public function testCreateNAGOutsideRange()
	{
		$move = new Move('e2', 'e3', PROMOTION_QUEEN, [1, 256, 3]);
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
	 * @expectedException     MoveException
	 * @expectedExceptionCode 7
	 */
	public function testGetDepartureInvalidFormat(Move $move)
	{
		$move->getDeparture(0.5);
	}

	/**
	 * @depends testCreateValidMove
	 * @expectedException     MoveException
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
