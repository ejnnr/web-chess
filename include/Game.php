<?php
/**
 * This file contains the class Game
 *
 * This file includes Position.php, Move.php and GameNode.php
 */

/**
 * Include necessary files
 */
require_once 'Position.php';

/**
 * Include necessary files
 */
require_once 'Move.php';

/**
 * Include necessary files
 */
require_once 'GameNode.php';

/**
 * A class representing a game of chess with variations.
 *
 */
class Game
{
	public function __construct(Position $startingPosition = null)
	{
		if ($startingPosition === null) {
			$startingPosition = new Position();
		}
		$this->startingPosition = $startingPosition;
		$this->children = [];
	}

	/**
	 * add a move at the current position of the game
	 *
	 * If there already is a move, a new variation will be added
	 *
	 * @param Move $move the move to add
	 * @return void
	 */
	public function doMove(Move $move)
	{
		// check if move is legal
		if (!$this->getPosition()->isLegalMove($move)) {
			throw new Exception('Trying to add illegal Move');
		}
		if (empty($this->current)) { // pointer at starting position
			$this->current = new GameNode($move);
			$this->children[] = $this->current;
			return;
		}
		$this->current = $this->current->addMove($move);
	}

	/**
	 * go back by one move
	 *
	 * This method does not delete any moves! It just sets an internal pointer back by one move.
	 *
	 * @return void
	 */
	public function back()
	{
		if ((!$this->current->isChild()) || empty($this->current)) { // current points to first move or starting position
			$this->current = null; // set current to starting position
			return;
		}

		$this->current = $this->current->getParent();
	}

	/**
	 * get the current position
	 *
	 * This method does not return the position at the end of the mainline!
	 *
	 * @return Position The current position
	 *
	 */
	public function getPosition()
	{
		if (empty($this->current)) { // current at starting position
			return $this->startingPosition;
		}
		return $this->current->positionAfter($this->startingPosition);
	}

	/**
	 * add a new variation of the last move that was played
	 *
	 * This is simply a combination of back() and doMove()
	 *
	 * @param Move $move The move to add as a variation
	 * @return void
	 */
	public function addVariation(Move $move)
	{
		$this->back();
		$this->doMove($move);
	}

	/**
	 * jump back to the position after the last call of startVariation()
	 *
	 * Actually it doesn't matter whether you added a variation with startVariation() or with back() and doMove()
	 *
	 * @return void
	 */
	public function endVariation()
	{
		while ($this->current->isMainlineContinuation()) { // go back until beginning of variation
			$this->back();
		}

		if (!$this->current->isChild()) { // current at first move
			$this->current = reset($this->children)->getMainlineContinuation();
			return;
		}

		$this->current = $this->current->getParent()->getMainlineContinuation();
	}
}
