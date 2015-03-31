<?php

require_once 'include/Position.php';

class PositionTest extends PHPUnit_Framework_TestCase
{
	public function testCreateValidPosition()
	{
		$position = new Position('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
		$this->assertInstanceOf('Position', $position);

		return $position;
	}

	/**
	 * @expectedException     PositionException
	 * @expectedExceptionCode 102
	 */
	public function testTooFewKings()
	{
		$position = new Position('8/pppppppp/8/8/8/8/PPPPPPPP/8 w - - 0 1');
	}

	/**
	 * @expectedException     PositionException
	 * @expectedExceptionCode 103
	 */
	public function testTooManyPiecesOfOneKind()
	{
		$position = new Position('8/QQQQQQQQ/QQ6/8/k7/7K/8/8 b - - 0 39');
	}

	/**
	 * @expectedException     PositionException
	 * @expectedExceptionCode 104
	 */
	public function testSideNotToMoveInCheck()
	{
	//	$position = new Position('7R/8/8/7k/8/8/8/K7 w - - 0 39');
		$this->markTestIncomplete();
	}

	/**
	 * @expectedException     PositionException
	 * @expectedExceptionCode 105
	 */
	public function testPawnsOnBackRank()
	{
		$position = new Position('p7/8/8/8/k7/8/7K/8 w - - 0 39');
	}

	/**
	 * @expectedException     PositionException
	 * @expectedExceptionCode 112
	 */
	public function testInvalidFENSyntax()
	{
		$position = new Position('k/8/8/K/8/8/8/8 w - - 0 1');
	}
	
	/**
	 * @depends testCreateValidPosition
	 */
	public function testGetFEN($positionObject)
	{
		$this->assertEquals('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1', $positionObject->getFEN());
	}
	
	/**
	 * @depends testCreateValidPosition
	 */
	public function testGetArray($positionObject)
	{
		$this->markTestIncomplete();
	}
}

?>
