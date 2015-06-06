<?php

/**
 * Copyright (c) dotBlue (http://dotblue.net)
 */

namespace DotBlue\WebImages;

use Latte;
use Latte\MacroNode;
use Latte\PhpWriter;


class Macros extends Latte\Macros\MacroSet
{
	/**
	 * @param Latte\Compiler $parser
	 */
	public static function install(Latte\Compiler $parser)
	{
		$me = new static($parser);
		$me->addMacro('src', function (MacroNode $node, PhpWriter $writer) use ($me) {
			return $me->macroSrc($node, $writer);
		}, NULL, function (MacroNode $node, PhpWriter $writer) use ($me) {
			return ' ?> src="<?php ' . $me->macroSrc($node, $writer) . ' ?>"<?php ';
		});
	}


	/********************* macros ****************v*d**/

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 */
	public function macroSrc(MacroNode $node, PhpWriter $writer)
	{
		$absolute = substr($node->args, 0, 2) === '//' ? '//' : '';
		$args = $absolute ? substr($node->args, 2) : $node->args;
		return $writer->write('echo %escape(%modify($_presenter->link("' . $absolute . ':Nette:Micro:", DotBlue\WebImages\Macros::prepareArguments([' . $args . ']))))');
	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public static function prepareArguments(array $arguments)
	{
		foreach ($arguments as $key => $value) {
			if ($key === 0 && !isset($arguments['id'])) {
				$arguments['id'] = $value;
				unset($arguments[$key]);
			} elseif ($key === 1 && !isset($arguments['namespace'])) {
				$arguments['namespace'] = $value;
				unset($arguments[$key]);
			} elseif ($key === 2 && !isset($arguments['type'])) {
				$arguments['type'] = $value;
				unset($arguments[$key]);
			}
		}
		if(!isset($arguments['prefix'])) {
			$arguments['prefix'] = substr($arguments['id'], 0, 2);
		}
		return $arguments;
	}

}
