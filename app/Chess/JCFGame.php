<?php namespace App\Chess;

class JCFGameException extends \Exception
{
}

/**
 * A class representing a game in JCF.
 *
 * @see Game
 */
class JCFGame extends Game implements \JsonSerializable
{
    /**
     * set current to a new GameNode.
     *
     * This overwrites the parent method to use JCFGameNode instead of GameNode
     *
     * @param Move $move
     *
     * @return void
     */
    protected function createCurrentNode(Move $move)
    {
        $this->current = new JCFGameNode($move);
    }

    /**
     * returns the JCF representations of the game.
     *
     * There's no difference between calling json_encode($game) and $game->getJCF().
     * This method is just for convienience.
     *
     * @return string
     */
    public function getJCF()
    {
        return json_encode($this);
    }

    /**
     * returns a representation of the class that can be parsed by json_encode().
     *
     * This means you can just write `json_encode(new JCFGame())` and will get the game in JCF
     *
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        if (empty($this->children)) {
            return ['meta' => $this->getHeaders(),
            'moves' => []];
        }

        $firstChild = reset($this->children);
        $moves = [];

        $moves[] = $this->encodeMove($firstChild->getMove(), $this->startingPosition->isPromotingMove($firstChild->getMove())); // encode the first move

        foreach ($this->children as $child) {
            if ($child === $firstChild) {
                continue; // skip if child is the node already encoded ($firstChild)
            }

            $moves[0]['variations'] = [];
            foreach ($node->getSiblings() as $sibling) {
                $moves[0]['variations'][] = $this->encodeNodeWithoutSiblings($sibling);
            }
        }

        if ($firstChild->hasChildren()) {
            $moves = array_merge($moves, $this->encodeNode($firstChild->getMainlineContinuation()));
        }

        return ['meta' => $this->getHeaders(),
                'moves' => $moves];
    }

    /**
     * encode a JCFGameNode recursively.
     *
     * @param JCFGameNode $node
     *
     * @return string The JCF representation of the node and its children as an array of moves
     */
    protected function encodeNode(JCFGameNode $node)
    {
        $ret = [];
        if ($node->isChild()) {
            $ret[] = $this->encodeMove($node->getMove(), $node->getParent()->positionAfter($this->startingPosition)->isPromotingMove($node->getMove()));
        } else {
            $ret[] = $this->encodeMove($node->getMove(), $this->startingPosition->isPromotingMove($node->getMove()));
        }

        if ($node->isChild() && $node->hasSiblings()) {
            $ret[0]['variations'] = [];
            foreach ($node->getSiblings() as $sibling) {
                $ret[0]['variations'][] = $this->encodeNodeWithoutSiblings($sibling);
            }
        }

        if ($node->hasChildren()) {
            $ret = array_merge($ret, $this->encodeNode($node->getMainlineContinuation()));
        }

        return $ret;
    }

    protected function encodeNodeWithoutSiblings(JCFGameNode $node)
    {
        $ret = [];
        if ($node->isChild()) {
            $ret[] = $this->encodeMove($node->getMove(), $node->getParent()->positionAfter($this->startingPosition)->isPromotingMove($node->getMove()));
        } else {
            $ret[] = $this->encodeMove($node->getMove(), $this->startingPosition->isPromotingMove($node->getMove()));
        }

        if ($node->hasChildren()) {
            $ret = array_merge($ret, $this->encodeNode($node->getMainlineContinuation()));
        }

        return $ret;
    }

    /**
     * encodes a Move object as a JCF move.
     *
     * @param Move $move
     * @param bool $promotion whether the move is promoting or not
     *
     * @return array The move in JCF format
     */
    public function encodeMove(Move $move, $promotion)
    {
        $moveArray = ['from' => $move->getDeparture(SQUARE_FORMAT_STRING),
                      'to'   => $move->getDestination(SQUARE_FORMAT_STRING), ];

        if ($promotion) {
            $moveArray['promotion'] = $move->getPromotion();
        }

        if (count($move->getNAGs()) > 0) {
            $moveArray['NAGs'] = $move->getNAGs();
        }

        if (!empty($move->getComment())) {
            $moveArray['comment'] = $move->getComment();
        }

        return $moveArray;
    }

    /**
     * load a jcf string.
     *
     * @param string $jcf String in JCF
     *
     * @return void
     */
    public function loadJCF($jcf)
    {
        if (is_string($jcf)) {
            $arr = json_decode($jcf, true);
        } elseif (is_array($jcf)) {
            $arr = $jcf;
        } else {
            throw new JCFGameException('jcf must be either of type string or of type array', 4);
        }

        if (is_null($arr)) {
            throw new JCFGameException('invalid  JSON: json_last_error():'.json_last_error(), 151);
        }

        if (!isset($arr['moves'])) {
            throw new JCFGameException('invalid JCF: no move list found', 152);
        }

        if (isset($arr['meta'])) {
            $this->setHeaders($arr['meta']);
        }

        $this->reset();

        foreach ($arr['moves'] as $move) {
            $this->addMoveArray($move);
        }

        return $this;
    }

    /**
     * decode a move in JCF.
     *
     * @param array $jcf The move as a JCF array
     *
     * @return Move
     */
    public function decodeMove($jcf)
    {
        if (!$this->validateMoveArray($jcf)) throw new JCFGameException('Invalid move');

        $promotionPiece = isset($jcf['promotion']) ? $jcf['promotion'] : PROMOTION_QUEEN;
        $NAGs = isset($jcf['NAGs']) ? $jcf['NAGs'] : [];
        $comment = isset($jcf['comment']) ? $jcf['comment'] : '';

        return new Move($jcf['from'], $jcf['to'], $promotionPiece, $NAGs, $comment);
    }

    /**
     * validate a JCF move array.
     *
     * @param array $move A move with childen in JCF
     *
     * @return bool true if valid, fals if not
     */
    protected function validateMoveArray($move)
    {
        if (!isset($move['from']) || !isset($move['to'])) {
            return false;
        }

        if (string2square($move['from']) === false || string2square($move['to']) === false) { // string2square returns false on invalid input
            return false;
        }

        return true;
    }

    /**
     * add a JCF move array with variations to the game.
     *
     * @param array $move
     *
     * @return void
     */
    protected function addMoveArray($moveArray)
    {
        $this->doMove($this->decodeMove($moveArray));

        if (isset($moveArray['commands'])) {
            foreach ($moveArray['commands'] as $command) {
                $this->current->addCommand($command['command'], $command['params']);
            }
        }

        if (isset($moveArray['variations'])) {
            foreach ($moveArray['variations'] as $child) {
                $this->back();
                $this->addMoveArray($child);
                $this->endVariation();
            }
        }
    }
}
