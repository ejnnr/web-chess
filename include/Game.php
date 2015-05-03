<?php

class Game
{

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
}
