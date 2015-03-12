<?php

/**
 * Contains the classes Move and MoveException
 */

/**
 * A class respresenting a exception thrown by Move
 *
 * List of Exception Codes:
 *  1: Unknown internal error
 *  2: Empty/Null argument
 *  3: Wrong argument type
 *  4: Invalid argument value (general)
 *  5: Invalid argument syntax (strings)
 *  6: Numerical argument outside of allowed range
 */

class MoveException extends Exception {}

/**
 * A class respresenting a chess move
 */

class Move
{

	/**
	 * Constructor of Move
	 *
	 * @param mixed $departure square of departure; can be in different formats, e.g 'a1' or array(0, 0)
	 */
	
	function __construct($departure, $destination)
	{
		
	}
}

?>
