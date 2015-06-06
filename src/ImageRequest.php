<?php

/**
 * Copyright (c) dotBlue (http://dotblue.net)
 */

namespace DotBlue\WebImages;

use Nette;


class ImageRequest extends Nette\Object
{

	/** @var string */
	private $id;

	/** @var string|NULL */
	private $namespace;

	/** @var string|NULL */
	private $type;

	/** @var int */
	private $format;

	/** @var array */
	private $parameters;



	/**
	 * @param  int
	 * @param  string
	 * @param  int|NULL
	 * @param  int|NULL
	 * @param  array
	 */
	public function __construct($format, $id, $namespace, $type, array $parameters)
	{
		$this->id = $id;
		$this->namespace = $namespace;
		$this->type = $type;
		$this->format = $format;
		$this->parameters = $parameters;
	}



	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}



	/**
	 * @return string|NULL
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}



	/**
	 * @return string|NULL
	 */
	public function getType()
	{
		return $this->type;
	}



	/**
	 * @return int
	 */
	public function getFormat()
	{
		return $this->format;
	}



	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

}
