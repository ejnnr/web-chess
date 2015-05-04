<?php

class GameNodeException extends Exception {}

/**
 * A class representing a node (i.e. a move) in a game of chess
 *
 * Uses https://github.com/nicmart/Tree
 */

class GameNode implements Tree\Node\NodeInterface
{
	use Tree\Node\NodeTrait
   	{
		__construct as traitConstruct;
	}

	public function __construct($move, array $children = [])
	{
		if (!($move instanceof Move)) {
			throw new GameNodeException('node value is no instance of Move', 4);
		}

		$this->traitConstruct($move, $children);
	}

	/**
 	 * returns the first child, i.e. the main move
 	 *
 	 * @return GameNode the mainline move or false if there are no children
 	 */

	public function getMainlineMove()
	{
		$children = $this->getChildren();
		return reset($children); // reset returns the first element of an array or false. See http://php.net/manual/en/function.reset.php
	}

	public function lastDescendant()
	{
		$current = $this;
		while (!$current->isLeaf()) { // getLeaf returns true if a node has no children
			$current = $current->getMainlineMove();
		}
		return $current;
	}
}
