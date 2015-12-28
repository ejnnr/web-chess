<?php namespace App\Chess;

/**
 * This file contains the class Game and the corresponding exception class.
 *
 * This file includes Position.php, Move.php and GameNode.php
 */

/**
 * A class representing an exception thrown by Game.
 */
class GameException extends \Exception
{
}

/**
 * A class representing a game of chess with variations.
 */
class Game
{
    /**
     * startingPosition.
     *
     * @var Position
     */
    protected $startingPosition;

    /**
     * children.
     *
     * @var GameNode[]
     */
    protected $children;

    /**
     * headers.
     *
     * @var mixed[]
     */
    protected $headers;

    public function __construct(Position $startingPosition = null)
    {
        if ($startingPosition === null) {
            $startingPosition = new Position();
        }
        $this->startingPosition = $startingPosition;
        $this->children = [];
        $this->headers = [];
    }

    /**
     * add a move at the current position of the game.
     *
     * If there already is a move, a new variation will be added
     *
     * @param Move $move the move to add
     *
     * @return void
     */
    public function doMove(Move $move)
    {
        // check if move is legal
        if (!$this->getPosition()->isLegalMove($move)) {
            throw new GameException('Trying to add illegal Move', 140);
        }
        if (empty($this->current)) { // pointer at starting position
            $this->createCurrentNode($move);
            $this->children[] = $this->current;

            return $this;
        }
        $this->current = $this->current->addMove($move);

        return $this;
    }

    /**
     * go back by one move.
     *
     * This method does not delete any moves! It just sets an internal pointer back by one move.
     *
     * @return void
     */
    public function back()
    {
        if ((!$this->current->isChild()) || empty($this->current)) { // current points to first move or starting position
            $this->current = null; // set current to starting position
            return;
        }

        $this->current = $this->current->getParent();
    }

    /**
     * get the current position.
     *
     * This method does not return the position at the end of the mainline!
     *
     * @return Position The current position
     */
    public function getPosition()
    {
        if (empty($this->current)) { // current at starting position
            return $this->startingPosition;
        }

        return $this->current->positionAfter($this->startingPosition);
    }

    /**
     * add a new variation of the last move that was played.
     *
     * This is simply a combination of back() and doMove()
     *
     * @param Move $move The move to add as a variation
     *
     * @return void
     */
    public function addVariation(Move $move)
    {
        $this->back();
        $this->doMove($move);
    }

    /**
     * jump back to the position after the last call of startVariation().
     *
     * Actually it doesn't matter whether you added a variation with startVariation() or with back() and doMove()
     *
     * @return void
     */
    public function endVariation()
    {
        while ($this->current->isMainlineContinuation()) { // go back until beginning of variation
            $this->back();
        }

        if (!$this->current->isChild()) { // current at first move
            $this->current = reset($this->children)->getMainlineContinuation();

            return;
        }

        $this->current = $this->current->getParent()->getMainlineContinuation();
    }

    /**
     * set a header.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function setHeader($name, $value)
    {
        if (!is_string($name)) {
            throw new GameException('name must be a string', 4);
        }

        $this->headers[$name] = $value;
    }

    /**
     * get the value of a specific header.
     *
     * @param mixed $name
     *
     * @return mixed The value of the header
     */
    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    /**
     * get all headers as an array.
     *
     * @return array An assoziative array of all the headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * set several headers at once.
     *
     * @param mixed[] $headers
     *
     * @return void
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

    public function goToEndOfMainline()
    {
        if (empty($this->children)) {
            return;
        }

        $this->current = reset($this->children);
        while ($this->current->hasChildren()) {
            $this->current = $this->current->getMainlineContinuation();
        }
    }

    /**
     * set current to a new GameNode.
     *
     * This needs to be a seperate method because child classes might want to use other types of Nodes (e.g. JCF)
     *
     * @param Move $move
     *
     * @return void
     */
    protected function createCurrentNode(Move $move)
    {
        $this->current = new GameNode($move);
    }

    /**
     * delete all the moves made and reset the game to its starting position.
     *
     * @return void
     */
    public function reset()
    {
        $this->children = [];
        $this->current = null;
    }
}
