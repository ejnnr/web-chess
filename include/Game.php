<?php

class Game
{
	private $moveCount;

	/**
 	 * the move tree
 	 */

	private $root;

	/**
 	 * the current position
 	 */

	private $position;

	/**
 	 * the constructor
 	 */
	public function __construct()
	{
		$this->position = new Position();
	}

	/**
 	 * returns the current Position
 	 *
 	 * @return Position the current position of the game
 	 */

	public function getPosition()
	{
		return $this->position;
	}

	/**
 	 * adds the move to the game and updates the pointer to the position after the new move
 	 *
 	 * @param Move $move the move to be played
 	 */

	public function doMove($move)
	{
		if (!($this->root instanceof GameNode)) {
			$this->root = new GameNode($move);
		} else {
			$this->root->lastDescendant()->addChild(new GameNode($move));
		}
		$this->moveCount++;
		$this->position->doMove($move);
	}

	/**
 	 * returns the count of moves in the mainline
 	 *
 	 * @return integer the count of moves
 	 */

	public function moveCount()
	{
		return $this->moveCount;
	}

	/**
 	 * returns the mainline as an array of moves
 	 *
 	 * @return Move[] the mainline of the game
 	 */

	public function getMainline()
	{
		$current = clone $this->root;
		$ret = [];
		while (!($current->isLeaf())) {
			$ret[] = $current->getValue();
			$current = $current->getMainlineMove();
		}
		$ret[] = $current->getValue();
		return $ret;
	}
}
