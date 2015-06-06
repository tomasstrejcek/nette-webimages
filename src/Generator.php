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

	/** @var array */
	private $config;

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
	public function __construct($config, Http\IRequest $httpRequest, Http\IResponse $httpResponse, Validator $validator)
	{
		$this->config = $config;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->validator = $validator;
	}


	/**
	 * @param IRepository $provider
	 */
	public function addRepository(IRepository $repository)
	{
		$repository->configure($this->config);
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
		$cfg =  $this->config['image'][$request->getNamespace()][$request->getType()];
		list($width, $height) = $cfg['size'];
		$quality = $cfg['quality'];
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

		/*if (!$image) {
			$this->httpResponse->setHeader('Content-Type', 'image/jpeg');
			$this->httpResponse->setCode(Http\IResponse::S404_NOT_FOUND);
			exit;
		}*/

		$destination = $this->config['wwwDir'] . '/' . $this->httpRequest->getUrl()->getRelativeUrl();

		$dirname = dirname($destination);
		$this->httpResponse->setHeader('Content-Type', 'text/plain');

		if (!is_dir($dirname)) {
			$success = @mkdir($dirname, 0777, TRUE);
			if (!$success) {
				throw new Application\BadRequestException;
			}
		}

		$success = $image->save($destination, $quality, $format);
		if (!$success) {
			throw new Application\BadRequestException;
		}

		$image->send();
		exit;
	}

}
