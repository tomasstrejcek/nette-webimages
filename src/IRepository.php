<?php

/**
 * Copyright (c) dotBlue (http://dotblue.net)
 */

namespace DotBlue\WebImages;


interface IRepository
{

	const FIT = 0;
	const EXACT = 1;
	const EXACT_HEIGHT_FIT_WIDTH = 2;

	/**
	 * @param $config array
	 * @return mixed
	 */
	function configure(array $config);

	/**
	 * @param ImageRequest $request
	 * @return mixed
	 */
	function getImage(ImageRequest $request);

	/**
	 * @param $file
	 * @return string
	 */
	function saveImage($file);

}
