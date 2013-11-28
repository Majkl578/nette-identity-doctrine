<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\Config;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\Configurator;
use Nette\Loaders\NetteLoader;

// Nette 2.0 & 2.1 compatibility, thx @HosipLan
if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
	class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
	class_alias('Nette\Config\Helpers', 'Nette\DI\Config\Helpers');
}
if (isset(NetteLoader::getInstance()->renamed['Nette\Configurator']) || !class_exists('Nette\Configurator')) {
	unset(NetteLoader::getInstance()->renamed['Nette\Configurator']);
	class_alias('Nette\Config\Configurator', 'Nette\Configurator');
}

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
		$name = self::NAME;
		$configurator->onCompile[] = function (Configurator $sender, Compiler $compiler) use ($name) {
			$compiler->addExtension($name, new Extension());
		};
	}
}
