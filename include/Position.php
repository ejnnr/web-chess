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
	/**
	 * Constructor of Position
	 *
	 * @param mixed $position The position to load as a FEN
	 */

	function __construct($position) // TODO: support other formats than FEN
	{

	}

	/**
	 * Returns the FEN of the position
	 *
	 * @return string The FEN
	 */

	function getFEN()
	{

	}

	/**
	 * Returns the position as an array: [['R', 'N', 'B', ...], ['P', 'P', ...], ..., ['r', 'n', ...]]
	 *
	 * @return array The position as an 8x8 array
	 */

	function getArray()
	{

	}

	/**
	 * parses the given FEN and loads it
	 *
	 * @param string $fen The FEN to be loaded
	 * @return true on success, false on failure
	 */

	function loadFEN($fen)
	{

	}

	/**
	 * Sets the position to the starting position
	 *
	 * @return true on success, false on failure
	 */

	function reset()
	{

	}

	/**
	 * Returns true if the game is draw by fifty moves rule
	 *
	 * Fifty moves must have been played without a piece being captured or a pawn beeing moved by either side
	 *
	 * @return boolean true if draw, false if not
	 */

	function isFiftyMoves()
	{

	}


	/**
	 * Returns true if the game is drawn by stalemate
	 *
	 * True if the side to move hasn't got any valid moves but isn't in check
	 *
	 * @return boolean true if draw, false if not
	 */

	function isStaleMate()
	{

	}


	/**
	 * Returns true if the side to move is checkmated
	 *
	 * @return boolean true if checkmate, false if not
	 */

	function isMate()
	{

	}


	/**
	 * Returns true if the side to move is in check
	 *
	 * @return boolean true if in check, false if not
	 */

	function inCheck()
	{

	}


	/**
	 * Check if the given move is legal
	 *
	 *
	 * @param Move $move the move to check
	 * @return boolean true if move is legal, false if not
	 */

	function isLegalMove($move)
	{

	}
}

?>
