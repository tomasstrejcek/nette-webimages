<?php

/**
 * Copyright (c) dotBlue (http://dotblue.net)
 */

namespace DotBlue\WebImages;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Utils\Image;


class Generator extends Nette\Object
{

	const FORMAT_JPEG = Image::JPEG;
	const FORMAT_PNG = Image::PNG;
	const FORMAT_GIF = Image::GIF;

	/** @var string */
	private $wwwDir;

	/** @var Http\IRequest */
	private $httpRequest;

	/** @var Http\IResponse */
	private $httpResponse;

	/** @var Validator */
	private $validator;

	/** @var IRepository[] */
	private $repositories = [];


	/**
	 * @param $wwwDir
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 * @param Validator $validator
	 */
	public function __construct($wwwDir, Http\IRequest $httpRequest, Http\IResponse $httpResponse, Validator $validator)
	{
		dump(func_get_args());
		$this->wwwDir = $wwwDir;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->validator = $validator;
	}


	/**
	 * @param IRepository $provider
	 */
	public function addRepository(IRepository $repository)
	{
		$this->repositories[] = $repository;
	}



	/**
	 * @return Validator
	 */
	public function getValidator()
	{
		return $this->validator;
	}


	/**
	 * @param ImageRequest $request
	 * @throws Application\BadRequestException
	 */
	public function generateImage(ImageRequest $request)
	{
		$width = $request->getWidth();
		$height = $request->getHeight();
		$format = $request->getFormat();

		if (!$this->validator->validate($width, $height)) {
			throw new Application\BadRequestException;
		}

		$image = NULL;

		/** @var IRepository $provider */
		foreach ($this->repositories as $provider) {
			/** @var Image $image */
			$image = $provider->getImage($request);
			if ($image) {
				break;
			}
		}

		if (!$image) {
			$this->httpResponse->setHeader('Content-Type', 'image/jpeg');
			$this->httpResponse->setCode(Http\IResponse::S404_NOT_FOUND);
			exit;
		}

		$destination = $this->wwwDir . '/' . $this->httpRequest->getUrl()->getRelativeUrl();

		$dirname = dirname($destination);
		if (!is_dir($dirname)) {
			$success = @mkdir($dirname, 0777, TRUE);
			if (!$success) {
				throw new Application\BadRequestException;
			}
		}

		$success = $image->save($destination, 90, $format);
		if (!$success) {
			throw new Application\BadRequestException;
		}

		$image->send();
		exit;
	}

}
