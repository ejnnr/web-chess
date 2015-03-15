<?php

/**
 * This file contains the class Position and the corresponding exception class.
 */

/**
 * Represents an exception thrown by Position.
 * List of Exception Codes:
 *   1: Unknown internal error
 *   2: Null argument
 *   3: Wrong argument count
 *   4: Wrong argument type
 *   5: Invalid argument value (general)
 *   6: Invalid argument syntax (strings)
 *   7: Numerical argument outside of allowed range
 * 101: Trying to load impossible position (general)
 * 102: Impossible position: one or two kings are missing
 * 103: Impossible position: too many pieces of one kind
 * 104: Impossible position: side not to move is in check
 * 104: Impossible position: both sides are in check
 * 105: Impossible position: pawns on 1st or 8th rank
 * 110: Invalid FEN syntax
 */

class PositionException extends Exception {}

class Position
{
	function __construct($position)
	{

	}

	function getFEN()
	{

	}

	function getArray()
	{

	}

	function loadFEN($fen)
	{

	}

	function reset()
	{

	}

	function isFiftyMoves()
	{

	}

	function isStaleMate()
	{

	}

	function isMate()
	{

	}

	function inCheck()
	{

	}

	function isLegalMove($move)
	{

	}
}

?>
