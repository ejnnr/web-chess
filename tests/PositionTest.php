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

	public function testIsLegalQueen()
	{
		$position = new Position('4k3/8/8/8/3Q4/8/8/4K3 w - - 0 1');
		$this->assertTrue($position->isLegalMove(new Move('d4', 'c3')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'a1')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'd8')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'h4')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'c2')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'e8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd4')));
	}
	
	public function testIsLegalRook()
	{
		$position = new Position('4k3/8/8/8/3R4/8/8/4K3 w - - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('d4', 'c3')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'a1')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'd8')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'h4')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'c2')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'e8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd4')));
	}
	
	public function testIsLegalBishop()
	{
		$position = new Position('4k3/8/8/8/3B4/8/8/4K3 w - - 0 1');
		$this->assertTrue($position->isLegalMove(new Move('d4', 'c3')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'a1')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'h4')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'c2')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'e8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd4')));
	}
	
	public function testIsLegalKnight()
	{
		$position = new Position('4k3/8/8/8/3N4/8/8/4K3 w - - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('d4', 'c3')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'a1')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'h4')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'c2')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'e2')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'f5')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'c6')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'e8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd4')));
	}
	
	public function testIsLegalKing()
	{
		$position = new Position('4k3/8/8/8/3K4/8/8/8 w - - 0 1');
		$this->assertTrue($position->isLegalMove(new Move('d4', 'c3')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'a1')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'h4')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'c2')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'e8')));
		$this->assertFalse($position->isLegalMove(new Move('d4', 'd4')));
		$this->assertTrue($position->isLegalMove(new Move('d4', 'd3')));
	}
	
	public function testIsLegalPawn()
	{
		$position = new Position('4k3/8/8/8/b3r1n1/1P3P1p/3PP2P/4K3 w - - 0 1');
		$this->assertTrue($position->isLegalMove(new Move('b3', 'a4')));
		$this->assertTrue($position->isLegalMove(new Move('b3', 'b4')));
		$this->assertTrue($position->isLegalMove(new Move('d2', 'd3')));
		$this->assertTrue($position->isLegalMove(new Move('d2', 'd4')));
		$this->assertTrue($position->isLegalMove(new Move('e2', 'e3')));
		$this->assertTrue($position->isLegalMove(new Move('f3', 'e4')));
		$this->assertTrue($position->isLegalMove(new Move('f3', 'g4')));
	
		$this->assertFalse($position->isLegalMove(new Move('b3', 'c4')));
		$this->assertFalse($position->isLegalMove(new Move('b3', 'b5')));
		$this->assertFalse($position->isLegalMove(new Move('b3', 'b2')));
		$this->assertFalse($position->isLegalMove(new Move('b3', 'a2')));
		$this->assertFalse($position->isLegalMove(new Move('e2', 'e4')));
		$this->assertFalse($position->isLegalMove(new Move('e2', 'f3')));
		$this->assertFalse($position->isLegalMove(new Move('h2', 'h3')));
		$this->assertFalse($position->isLegalMove(new Move('h2', 'h4')));
	}
	
	public function testIsLegalEnPassant()
	{
		$position = new Position('4k3/8/1P6/1PpP1pP1/8/8/8/4K3 w - c6 0 1');
		$this->assertTrue($position->isLegalMove(new Move('b5', 'c6')));
		$this->assertTrue($position->isLegalMove(new Move('d5', 'c6')));
		$this->assertFalse($position->isLegalMove(new Move('b6', 'c6')));
		$this->assertFalse($position->isLegalMove(new Move('g5', 'f6')));
	}
	
	public function testIsLegalCastling()
	{
		$position = new Position('4k3/8/8/8/8/8/8/R3K2R w KQ - 0 1');
		$this->assertTrue($position->isLegalMove(new Move('e1', 'g1')));
		$this->assertTrue($position->isLegalMove(new Move('e1', 'c1')));
		
		$position = new Position('4k3/8/8/8/4r3/8/8/R3K2R w KQ - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'g1')));
		$this->assertFalse($position->isLegalMove(new Move('e1', 'c1')));
		
		$position = new Position('4k3/8/8/8/5r2/8/8/R3K2R w KQ - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'g1')));
		
		$position = new Position('4k3/8/8/8/3r4/8/8/R3K2R w KQ - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'c1')));
		
		$position = new Position('4k3/8/8/8/6r1/8/8/R3K2R w KQ - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'g1')));
		
		$position = new Position('4k3/8/8/8/2r5/8/8/R3K2R w KQ - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'c1')));
		
		$position = new Position('4k3/8/8/8/8/8/8/R3K2R w - - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'g1')));
		$this->assertFalse($position->isLegalMove(new Move('e1', 'c1')));
	}
	
	public function testIsLegalLeavingKingInCheck()
	{
		$position = new Position('4k3/8/8/3rr3/8/8/4B3/4K3 w - - 0 1');
		$this->assertFalse($position->isLegalMove(new Move('e1', 'd1')));
		$this->assertFalse($position->isLegalMove(new Move('e2', 'd1')));
	}
}

?>
