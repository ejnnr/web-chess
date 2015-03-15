<?php
require_once 'include/Move.php';

class MoveTest extends PHPUnit_Framework_TestCase
{
	public function testCreateValidMove()
	{
		$this->assertInstanceOf('Move', new Move('a1', 'a2'));
		$move = new Move(0, 8);
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
	 * @expectedExceptionCode 3
	 */
	public function testCreateMoveWrongArgumentCount()
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
}
?>
