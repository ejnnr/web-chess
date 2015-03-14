<?php

/**
 * Contains functions and constants to work with squares
 */

/**
 * converts formats like 'a4' to an integer
 *
 * Be careful: this function can return 0 or false, so use === to check the result
 *
 * @param string $square the square to convert in SAN format , e.g. 'e4'
 * @return int an integer; bit 0 to 2 are the file number, bit 3 to 5 are the rank number and bit 6 and 7 are always zero (in LSB 0); returns false on failure
 */

function string2square ($square)
{
	$regExpResult = preg_match('/[a-h][1-8]/', $square);

	if ($regExpResult === FALSE) { // error occurred in preg_match
		return FALSE;
	}

	if ($regExpResult === 0) { // $square has no valid syntax
		return FALSE;
	}

	$fileNumber = 0;
	switch (substr($square, 0, 1)) {
		case 'a': $fileNumber = 0; break;
		case 'b': $fileNumber = 1; break;
		case 'c': $fileNumber = 2; break;
		case 'd': $fileNumber = 3; break;
		case 'e': $fileNumber = 4; break;
		case 'f': $fileNumber = 5; break;
		case 'g': $fileNumber = 6; break;
		case 'h': $fileNumber = 7; break;
		default: return FALSE; // should actually never happen, since we already used preg_match
	}

	return $fileNumber + ((int)substr($square, 1, 1) - 1) * 8; // put together the return value; see phpdoc for more details
}

/**
 * converts formats integers created by string2square() to readable  strings like 'a4'
 *
 * @param int $square the square to convert
 * @return string the square as a string; false on failure
 */

function square2string ($square)
{
	if (!is_int($square)) {
		return FALSE;
	}

	if (($square > 63) || ($square < 0)) {
		return FALSE;
	}

	$file = '';
	switch ($square & 7) { // get the three least significant bits
		case 0: $file = 'a'; break;
		case 1: $file = 'b'; break;
		case 2: $file = 'c'; break;
		case 3: $file = 'd'; break;
		case 4: $file = 'e'; break;
		case 5: $file = 'f'; break;
		case 6: $file = 'g'; break;
		case 7: $file = 'h'; break;
		default: return FALSE; // should never happen
	}

	return $file . (string)((($square & 56) / 8 + 1)); // take the three bits where the rank is saved and divide by 8; hint: 56 is 2^5 + 2^4 + 2^3
}

?>
