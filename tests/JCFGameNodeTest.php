<?php namespace App\Chess;

class JCFGameNodeTest extends \TestCase
{
	public function testCommands()
	{
		$node = new JCFGameNode(new Move('c2', 'c4'));
		$node->addCommand('timeSpent', ['time' => '00:00:03']);
		$node->addCommand('diagram');
		$this->assertEquals([
				['command' => 'timeSpent', 'params' => ['time' => '00:00:03']],
				['command' => 'diagram', 'params' => []]
			], $node->getCommands());
	}
}
