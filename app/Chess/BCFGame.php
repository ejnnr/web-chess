<?php namespace App\Chess;

class BCFGameException extends \Exception {}

class BCFGame extends JCFGame
{
	public function getBCF()
	{
		if (empty($this->children)) {
			return '';
		}

		$firstChild = reset($this->children);

		$ret = $this->encodeMove($firstChild->getMove(), $this->startingPosition->isPromotingMove($firstChild->getMove()));

		foreach ($this->children as $child) {
			if ($child === $firstChild) {
				continue;
			}

			$ret .= dechex((1 << 7) + 2); // start variation
			$ret .= $this->encodeNodewithoutSiblings($child);
			$ret .= dechex((1 << 7) + 3); // end variation
		}

		if ($firstChild->hasChildren()) {
 		   	$ret .= $this->encodeNode($firstChild->getMainlineContinuation());
		}

		return $ret;
	}

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

	public function loadBCF($bcf)
	{

	}

	protected function encodePlainMove(Move $move, $promotion)
	{
		$ret = 0;

		$ret |= ((int)$promotion << 14); // shift the promotion flag to position 15 and add it to the return value

		if ($promotion) {
			$ret |= (($move->getPromotion() - 1) << 6);
		}

		$ret |= ($move->getDeparture(SQUARE_FORMAT_INT) << 8);
		$ret |= $move->getDestination(SQUARE_FORMAT_INT);

		return $ret;
	}

	public function encodeMove(Move $move, $promotion)
	{
		$ret = dechex($this->encodePlainMove($move, $promotion));

		while (strlen($ret) < 4) {
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
			$ret .= dechex((1 << 7) + 4);

			foreach (str_split($move->getComment()) as $char) {
				if (ord($char) < 16) {
					$ret .= '0';
				}
				$ret .= dechex(ord($char));
			}

			$ret .= dechex((1 << 7) + 5);
		}

		return $ret;
	}
}
