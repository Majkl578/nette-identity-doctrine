<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\Config;

use Nette\Config\Compiler;
use Nette\Config\CompilerExtension;
use Nette\Config\Configurator;

/**
 * @author Michael Moravec
 */
class Extension extends CompilerExtension
{
	const NAME = 'doctrine2identity';

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('nette.userStorage')
			->setClass('Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage');
	}

	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $sender, Compiler $compiler) {
			$compiler->addExtension(self::NAME, new static());
		};
	}
}
