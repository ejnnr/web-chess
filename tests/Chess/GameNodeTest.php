<?php namespace App\Chess;

class GameNodeTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateGameNode()
	{
		$node = new GameNode(new Move('e2', 'e4'));
		$this->assertInstanceOf('App\Chess\GameNode', $node);
	}

	public function testGetMainline()
	{
		$node = new GameNode(new Move('e2', 'e4'));
		$node->attachChild($node1 = new GameNode(new Move('e7', 'e5')));
		$node->attachChild($node2 = new GameNode(new Move('c7', 'c5')));
		$this->assertEquals($node1, $node->getMainlineContinuation());
	}

	public function testGetMainlineWithoutChildren()
	{
		$node = new GameNode(new Move('d2', 'd4'));
		$this->assertFalse($node->getMainlineContinuation());
	}
}
