<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\DI;

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
class IdentityExtension extends CompilerExtension
{
	const NAME = 'doctrine2identity';

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$originalStorage = $builder->getDefinition('nette.userStorage');
		$decoratorStorage = clone $originalStorage;

		// original storage replaced by our own storage, but is kept as different service
		$builder->addDefinition($this->prefix('originalUserStorage'), $originalStorage)
			->setAutowired(FALSE);

		$builder->removeDefinition('nette.userStorage');
		$builder->addDefinition('nette.userStorage', $decoratorStorage)
			->setClass('Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage', array('@' . $this->prefix('originalUserStorage')));
	}

	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $sender, Compiler $compiler) {
			$compiler->addExtension(IdentityExtension::NAME, new IdentityExtension());
		};
	}
}
