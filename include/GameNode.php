<?php

class GameNodeException extends Exception {}

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

	public function positionAfter(Position $startingPosition)
	{
		if (!$this->isChild()) {
			$pos = clone $startingPosition;
			return $pos->doMove($this->move);
		}
		return $this->parent->positionAfter($startingPosition)->doMove($this->move);
	}

	public function addChild(GameNode $child)
	{
		$this->children[] = $child;
	}

	public function attachChild(GameNode $child)
	{
		$child->attachTo($this);
		return $child;
	}

	public function attachTo(GameNode $parent)
	{
		$this->parent = $parent;
		$this->parent->addChild($this);
	}

	public function addMove(Move $move)
	{
		return $this->attachChild(new GameNode($move));
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function isChild()
	{
		return !empty($this->parent);
	}

	public function getMainlineContinuation()
	{
		return reset($this->children);
	}

	public function isMainlineContinuation()
	{
		if (!$this->isChild()) {
			return true;
		}
		return $this == $this->parent->getMainlineContinuation();
	}
}
