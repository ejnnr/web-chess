<?php

/**
 * This file contains the class GameNode and the corresponding exception class.
 *
 * This file includes Position.php and Move.php.
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
 * A class representing an exception thrown by GameNode
 */
class GameNodeException extends Exception {}

/**
 * A class representing a move in a game
 *
 * This class is used by Game. In most cases you can simply use that class and don't really need to care how this one works.
 *
 */
class GameNode
{
	public function __construct(Move $move, GameNode $parent = null)
	{
		$this->move = $move;
		if (!empty($parent)) {
			$this->attachTo($parent);
		}
		$this->children = [];
	}

	/**
	 * get the position after the move
	 *
	 * This method traverses the whole tree back to its root to calculate the position
	 *
	 * @param Position $startingPosition The position the root node starts with
	 * @return Position The position after the move
	 */
	public function positionAfter(Position $startingPosition)
	{
		if (!$this->isChild()) { // node has no parent
			$pos = clone $startingPosition;
			return $pos->doMove($this->move);
		}
		return $this->parent->positionAfter($startingPosition)->doMove($this->move);
	}

	/**
	 * add another GameNode as a child to this one
	 *
	 * Be careful! This method does not set the parent attribute of the added node! It is recommended to use attachCild() instead.
	 *
	 * @param GameNode $child The GameNode to add
	 * @return void
	 */
	public function addChild(GameNode $child)
	{
		$this->children[] = $child;
	}

	/**
	 * add another GameNode as a child an set its parent attribute
	 *
	 * @param GameNode $child The GameNode to add
	 * @return GameNode The child added
	 */
	public function attachChild(GameNode $child)
	{
		$child->attachTo($this);
		return $child;
	}

	/**
	 * add the GameNode as a child to another one
	 *
	 * @param GameNode $parent
	 * @return void
	 */
	public function attachTo(GameNode $parent)
	{
		$this->parent = $parent;
		$this->parent->addChild($this);
	}

	/**
	 * create a new GameNode and attach it to this one
	 *
	 * @param Move $move
	 * @return GameNode The new node
	 */
	public function addMove(Move $move)
	{
		return $this->attachChild(new GameNode($move));
	}

	/**
	 * get the parent of the node
	 *
	 * @return GameNode the parent node
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * return whether the node has a parent node or not
	 *
	 * @return bool whether the node is a child or not
	 */
	public function isChild()
	{
		return !empty($this->parent);
	}

	/**
	 * get the first child of the node
	 *
	 * @return mixed The mainline continuation or false if the node has no children
	 */
	public function getMainlineContinuation()
	{
		return reset($this->children);
	}

	/**
	 * return whether the node is the mainline continuation of its parent node
	 *
	 * @return bool Also returns true if the node has no parents!
	 */
	public function isMainlineContinuation()
	{
		if (!$this->isChild()) {
			return true;
		}
		return $this == $this->parent->getMainlineContinuation();
	}
}
