<?php

class JCFGameNodeException extends Exception {}

/**
 * A class representing a node in a JCF game
 *
 * @see GameNode
 */
class JCFGameNode extends GameNode
{

	/**
	 * a list of all commands allowed by the JCF standard
	 *
	 * TODO: actually use this list for validation
	 *
	 * @var string[]
	 */
	protected static $allowedCommands = [
		'timeAfter',
		'timeSpent',
		'highlight',
		'arrow',
		'diagram',
		'evaluation'
		];

	/**
	 * an array of commands. See https://github.com/jupiter24/web-chess/wiki/JCF#commands for details
	 *
	 * @var mixed[]
	 */
	protected $commands = [];

	/**
	 * create a new JCFGameNode and attach it to this one
	 *
	 * @param Move $move
	 * @return JCFGameNode The new node
	 */
	public function addMove(Move $move)
	{
		return $this->attachChild(new JCFGameNode($move));
	}

	/**
	 * add a new command
	 *
	 * @param string  $command The name of the command to add
	 * @param mixed[] $params  The parameters of the command
	 * @return void
	 */
	public function addCommand($command, $params = [])
	{
		$this->commands[] = ['command' => $command, 'params' => $params];
	}

	/**
	 * get a list of all commands with parameters
	 *
	 * @return mixed[]
	 */
	public function getCommands()
	{
		return $this->commands;
	}
}
