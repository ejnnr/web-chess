<?php

require_once 'include/GameNode.php';

class GameNodeTest extends PHPUnit_Framework_TestCase
{
	public function testCreateGameNode()
	{
		$node = new GameNode(new Move('e2', 'e4'));
		$this->assertInstanceOf('GameNode', $node);
	}

	public function testImplementsNodeInterface()
	{
		$node = new GameNode(new Move('e2', 'e4'));
		$this->assertInstanceOf('Tree\Node\NodeInterface', $node);
	}

	public function testGetMainline()
	{
		$node = new GameNode(new Move('e2', 'e4'));
		$node->addChild($node1 = new GameNode(new Move('e7', 'e5')));
		$node->addChild($node2 = new GameNode(new Move('c7', 'c5')));
		$this->assertEquals($node1, $node->getMainline());
	}

	public function testGetMainlineWithoutChildren()
	{
		$node = new GameNode(new Move('d2', 'd4'));
		$this->assertFalse($node->getMainline());
	}

	/**
 	 * @expectedException     GameNodeException
 	 * @expectedExceptionCode 4
 	 */
	public function testCreateInvalidNode()
	{
		$node = new GameNode('test');
	}

	public function testCreateNodeWithChildren()
	{
		$node = new GameNode(new Move('e2', 'e4'), [new GameNode(new Move('e7', 'e5'))]);
		$this->assertSame(1, count($node->getChildren()));
	}
}
