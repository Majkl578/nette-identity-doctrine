<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

/**
 * @author Michael Moravec
 */
class IdentityExtension extends CompilerExtension
{
	const NAME = 'doctrine2identity';

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$userStorageDefinitionName = $builder->getByType('Nette\Security\IUserStorage') ?: 'nette.userStorage';
		$builder->getDefinition($userStorageDefinitionName)
			->setFactory('Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage');
	}

	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $sender, Compiler $compiler) {
			$compiler->addExtension(IdentityExtension::NAME, new IdentityExtension());
		};
	}
}
