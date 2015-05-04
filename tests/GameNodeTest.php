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
		$this->assertEquals($node1, $node->getMainlineMove());
	}

	public function testGetMainlineWithoutChildren()
	{
		$node = new GameNode(new Move('d2', 'd4'));
		$this->assertFalse($node->getMainlineMove());
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

	public function testGetLastChild()
	{
		$node = new GameNode(new Move('d2', 'd4'));
		$node->addChild($node2 = new GameNode(new Move('d7', 'd5')));
		$node2->addChild($node3 = new GameNode(new Move('c2', 'c4')));
		$node3->addChild($last = new GameNode(new Move('c7', 'c6')));
		$this->assertEquals($last, $node->lastDescendant());
	}

	public function testAddAtEnd()
	{
		$node = new GameNode(new Move('d2', 'd4'));
		$node->addChild($node2 = new GameNode(new Move('d7', 'd5')));
		$node2->lastDescendant()->addChild($node3 = new GameNode(new Move('c2', 'c4')));
		$node->lastDescendant()->addChild($last = new GameNode(new Move('c7', 'c6')));
		$this->assertEquals($last, $node->lastDescendant());
	}

	public function testLastDescendantIsSelf()
	{
		$node = new GameNode(new Move('b2', 'b3'));
		$this->assertEquals($node, $node->lastDescendant());
	}
}
