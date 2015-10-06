<?php

class Game
{
	/**
 	 * the move tree
 	 */

	private $root;

	/**
 	 * the constructor
 	 */
	public function __construct()
	{
	}

	/**
 	 * returns the current Position
 	 *
 	 * @return Position the current position of the game
 	 */

	public function getPosition()
	{
		$moves = $this->getMainline();
		$position = new Position();

		foreach ($moves as $move) {
			$position->doMove($move);
		}

		return $position;
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
	}

	/**
 	 * returns the count of moves in the mainline
 	 *
 	 * @return integer the count of moves
 	 */

	public function moveCount()
	{
		return count($this->getMainline());
	}

	/**
 	 * returns the mainline as an array of moves
 	 *
 	 * @return Move[] the mainline of the game
 	 */

	public function getMainline()
	{
		if (!($this->root instanceof GameNode)) {
			return [];
		}

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
