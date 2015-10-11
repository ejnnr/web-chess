<?php

class Game
{
	/**
 	 * the move tree
 	 */

	private $root;

	/**
 	 * the current node
 	 */

	private $currentNode;

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
		if (!($this->currentNode instanceof GameNode)) {
			return new Position();
		}
		$moves = [];
		$iterator = $this->currentNode;
		while (!($iterator == $this->root)) {
			$moves[] = $iterator->getValue();
			$iterator = $iterator->getParent();
		}
		$moves[] = $iterator->getValue();
		$moves = array_reverse($moves);
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
		if (!($this->currentNode instanceof GameNode)) {
			$this->currentNode = new GameNode($move);
			$this->root = $this->currentNode;
		} else {
			$newNode = new GameNode($move);
			$this->currentNode->addChild($newNode);
			// set currentNode to its most lately added child (i.e. the node just added)
			$this->currentNode = $newNode;
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

	/**
 	 * sets the pointer back one move
 	 */

	public function back()
	{
		$this->currentNode = $this->currentNode->getParent();
	}

	/**
 	 * creates a new variation of the last move
 	 */

	public function addVariation($move)
	{
		$this->back();
		$this->doMove($move);
	}

	/**
 	 * jumps back to the position before the last call of addVariation()
 	 */

	public function endVariation()
	{
		if (!$this->currentNode->isChild()) {
			$this->currentNode = $this->currentNode->getMainlineMove();
			return;
		}
		while ($this->currentNode != $this->currentNode->getParent()->getMainlineMove()) {
			$this->back();
			if (!$this->currentNode->isChild()) {
				$this->currentNode = $this->currentNode->getMainlineMove();
				return;
			}
		}

		$this->currentNode = $this->currentNode->getParent()->getMainlineMove();
	}
}
