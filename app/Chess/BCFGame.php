<?php namespace App\Chess;

class BCFGameException extends \Exception {}

class BCFGame extends JCFGame
{
	/**
	 * get the game in BCF
	 *
	 * @return string The hex representation of the game in BCF. See https://github.com/jupiter24/web-chess/wiki/BCF
	 */
	public function getBCF()
	{
		if (empty($this->children)) {
			return '';
		}

		$firstChild = reset($this->children);

		$ret = $this->encodeMove($firstChild->getMove(), $this->startingPosition->isPromotingMove($firstChild->getMove())); // encode the first move

		foreach ($this->children as $child) {
			if ($child === $firstChild) {
				continue; // skip if child is the node already encoded ($firstChild)
			}

			$ret .= dechex((1 << 7) + 2); // start variation
			$ret .= $this->encodeNodeWithoutSiblings($child); // this method must be used because the siblings are handled in this very loop.
			                                                  // Using encodeNode() would result in an infinite loop
			$ret .= dechex((1 << 7) + 3); // end variation
		}

		if ($firstChild->hasChildren()) {
 		   	$ret .= $this->encodeNode($firstChild->getMainlineContinuation()); // recursively encode all following moves
			                                                                   // Note that the other children are handled as siblings in the next cycle
		}

		return $ret;
	}

	/**
	 * encode a JCFGameNode recursively
	 *
	 * @param JCFGameNode $node
	 * @return string The hex representation of the node in BCF
	 */
	protected function encodeNode(JCFGameNode $node)
   	{
		if ($node->isChild()) {
			$ret = $this->encodeMove($node->getMove(), $node->getParent()->positionAfter($this->startingPosition)->isPromotingMove($node->getMove()));
		} else {
			$ret = $this->encodeMove($node->getMove(), $this->startingPosition->isPromotingMove($node->getMove()));
		}

		if ($node->isChild()) {
			foreach ($node->getSiblings() as $sibling) {
				$ret .= dechex((1 << 7) + 2); // start variation
				$ret .= $this->encodeNodewithoutSiblings($sibling);
				$ret .= dechex((1 << 7) + 3); // end variation
			}
		}

		if ($node->hasChildren()) {
 		   	$ret .= $this->encodeNode($node->getMainlineContinuation());
		}

		return $ret;
	}

	protected function encodeNodeWithoutSiblings(JCFGameNode $node)
	{
		if ($node->isChild()) {
			$ret = $this->encodeMove($node->getMove(), $node->getParent()->positionAfter($this->startingPosition)->isPromotingMove($node->getMove()));
		} else {
			$ret = $this->encodeMove($node->getMove(), $this->startingPosition->isPromotingMove($node->getMove()));
		}

		if ($node->hasChildren()) {
 		   	$ret .= $this->encodeNode($node->getMainlineContinuation());
		}

		return $ret;
	}

	/**
	 * load a game in BCF
	 *
	 * @param string $bcf The game in hex representation
	 * @return void
	 */
	public function loadBCF($bcf)
	{

	}

	/**
	 * encodes a Move object ignoring NAGs/comments
	 *
	 * @param Move $move
	 * @param bool $promotion whether the move is promoting or not
	 * @return string a 2-byte value in hex representation (i.e. 4 chars)
	 */
	protected function encodePlainMove(Move $move, $promotion)
	{
		$ret = 0;

		$ret |= ((int)$promotion << 14); // shift the promotion flag to position 15 and add it to the return value

		if ($promotion) {
			$ret |= (($move->getPromotion() - 1) << 6); // the `- 1` is needed because the values returned by getPromotion() start at 1,
			                                            // so a knight would be 4 which doesn't fit into two bits
		}

		$ret |= ($move->getDeparture(SQUARE_FORMAT_INT) << 8); // the departure square must be shiftet to the end of the first byte
		$ret |= $move->getDestination(SQUARE_FORMAT_INT);

		return $ret;
	}

	/**
	 * encodes a Move object as a BCF move
	 *
	 * @param Move $move
	 * @param bool $promotion whether the move is promoting or not
	 * @return string A hex representation starting with the move and followed by annotations
	 */
	public function encodeMove(Move $move, $promotion)
	{
		$ret = dechex($this->encodePlainMove($move, $promotion));

		while (strlen($ret) < 4) { // if the move has a low integer value, the hex representation will be shorter than 4 letter. This would lead to problems when concatenating moves later.
			$ret = '0' . $ret;
		}

		if (!empty($move->getNAGs())) {
			
			foreach ($move->getNAGs() as $nag) {
				$annotation = 0;
				$annotation |= (1 << 15);

				$annotation |= (1 << 8);
				$annotation |= $nag;
				$ret .= dechex($annotation);
			}
		}

		if (!empty($move->getComment())) {
			$ret .= dechex((1 << 7) + 4); // start comment

			foreach (str_split($move->getComment()) as $char) {
				if (ord($char) < 16) {
					$ret .= '0';
				}
				$ret .= dechex(ord($char));
			}

			$ret .= dechex((1 << 7) + 5); // end comment
		}

		return $ret;
	}
}
