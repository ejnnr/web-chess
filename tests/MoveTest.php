<?php
require 'include/Move.php';

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
	public function testCreateMoveEmptyArgument()
	{
		$move = new Move('', 'e3');
	}

	/**
	 * @depends testCreateValidMove
	 */
	public function testGetDeparture(Move $move)
	{
		$this->assertSame(0, $move->getDeparture(SQUARE_FORMAT_INT));
		$this->assertSame(0, $move->getDeparture(SQUARE_FORMAT_STRING));
		$this->assertSame(0, $move->getDeparture(SQUARE_FORMAT_ARRAY));
	}

	/**
	 * @depends testCreateValidMove
	 */
	public function testGetDestination(Move $move)
	{
		$this->assertSame(8, $move->getDestination(SQUARE_FORMAT_INT));
		$this->assertSame(8, $move->getDestination(SQUARE_FORMAT_STRING));
		$this->assertSame(8, $move->getDestination(SQUARE_FORMAT_ARRAY));
	}
}
?>
