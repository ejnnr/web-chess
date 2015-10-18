<?php

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

	public function doMove(Move $move)
	{
		if (!$this->getPosition()->isLegalMove($move)) {
			throw new Exception('Trying to add illegal Move');
		}
		if (empty($this->current)) {
			$this->current = new GameNode($move);
			$this->children[] = $this->current;
			return;
		}
		$this->current = $this->current->addMove($move);
	}

	public function back()
	{
		if ((!$this->current->isChild()) || empty($this->current)) {
			$this->current = null;
			return;
		}

		$this->current = $this->current->getParent();
	}

	public function getPosition()
	{
		if (empty($this->current)) {
			return $this->startingPosition;
		}
		return $this->current->positionAfter($this->startingPosition);
	}

	public function addVariation(Move $move)
	{
		$this->back();
		$this->doMove($move);
	}

	public function endVariation()
	{
		while ($this->current->isMainlineContinuation()) {
			$this->back();
		}

		if (!$this->current->isChild()) {
			$this->current = reset($this->children)->getMainlineContinuation();
			return;
		}

		$this->current = $this->current->getParent()->getMainlineContinuation();
	}
}
