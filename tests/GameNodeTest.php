<?php

require_once 'include/GameNode.php';

class GameNodeTest extends PHPUnit_Framework_TestCase
{
	public function testCreateGameNode()
	{
		$node = new GameNode();
		$this->assertInstanceOf('GameNode', $node);
	}

	public function testImplementsNodeInterface()
	{
		$node = new GameNode();
		$this->assertInstanceOf('Tree\Node\NodeInterface', $node);
	}
}
