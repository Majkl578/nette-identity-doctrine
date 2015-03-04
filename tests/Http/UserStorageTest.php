<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\Tests\Http;

use Doctrine\ORM\EntityManager;
use Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage;
use Majkl578\NetteAddons\Doctrine2Identity\Tests\ContainerFactory;
use Majkl578\NetteAddons\Doctrine2Identity\Tests\DatabaseLoader;
use Nette;
use Nette\DI\Container;
use Nette\Security\Identity;
use PHPUnit_Framework_TestCase;

class UserStorageTest extends PHPUnit_Framework_TestCase
{
	/** @var Container */
	private $container;

	/** @var UserStorage */
	private $userStorage;

	/** @var EntityManager */
	private $entityManager;

	/** @var DatabaseLoader */
	private $databaseLoader;


	public function __construct()
	{
		$containerFactory = new ContainerFactory;
		$this->container = $containerFactory->create();
	}

	protected function setUp()
	{
		$this->userStorage = $this->container->getByType('Nette\Security\IUserStorage') ?:
			$this->container->getService('nette.userStorage');
		$this->entityManager = $this->container->getBytype('Doctrine\ORM\EntityManager');
		$this->databaseLoader = $this->container->getByType('Majkl578\NetteAddons\Doctrine2Identity\Tests\DatabaseLoader');
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nette\Security\IUserStorage', $this->userStorage);
		$this->assertInstanceOf('Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage', $this->userStorage);
	}

	public function testGetIdentity()
	{
		$this->assertNull($this->userStorage->getIdentity());
	}

	public function testSetIdentity()
	{
		$this->userStorage->setIdentity(new Identity(1));
	}

	public function testEntityIdentity()
	{
		$this->databaseLoader->loadUserTableWithOneItem();
		$userRepository = $this->entityManager->getRepository('Majkl578\NetteAddons\Doctrine2Identity\Tests\Entities\User');
		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($user);
		$this->assertInstanceOf('Nette\Security\IUserStorage', $userStorage);
		$this->assertInstanceOf('Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage', $userStorage);

		$userIdentity = $userStorage->getIdentity();
		$this->assertSame($user, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame(array(), $userIdentity->getRoles());
	}
}
