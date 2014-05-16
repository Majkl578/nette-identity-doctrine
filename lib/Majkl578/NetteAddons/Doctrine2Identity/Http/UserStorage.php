<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\Http;

use Doctrine\ORM\EntityManager;
use Majkl578\NetteAddons\Doctrine2Identity\Security\FakeIdentity;
use Nette\Object;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;

/**
 * @author Michael Moravec
 */
class UserStorage extends Object implements IUserStorage
{
	/** @var IUserStorage */
	private $userStorage;

	/** @var EntityManager */
	private $entityManager;

	public function  __construct(IUserStorage $userStorage, EntityManager $entityManager)
	{
		$this->userStorage = $userStorage;
		$this->entityManager = $entityManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setIdentity(IIdentity $identity = NULL)
	{
		if ($identity !== NULL) {
			$class = get_class($identity);

			// we want to convert identity entities into fake identity
			// so only the identifier fields are stored,
			// but we are only interested in identities which are correctly
			// mapped as doctrine entities
			if ($this->entityManager->getMetadataFactory()->hasMetadataFor($class)) {
				$cm = $this->entityManager->getClassMetadata($class);
				$identifier = $cm->getIdentifierValues($identity);
				$identity = new FakeIdentity($identifier, $class);
			}
		}

		return $this->userStorage->setIdentity($identity);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIdentity()
	{
		$identity = $this->userStorage->getIdentity();

		// if we have our fake identity, we now want to
		// convert it back into the real entity
		// returning reference provides potentially lazy behavior
		if ($identity instanceof FakeIdentity) {
			return $this->entityManager->getReference($identity->getClass(), $identity->getId());
		}

		return $identity;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAuthenticated($state)
	{
		return $this->userStorage->setAuthenticated($state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAuthenticated()
	{
		return $this->userStorage->isAuthenticated();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setExpiration($time, $flags = 0)
	{
		return $this->userStorage->setExpiration($time, $flags);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLogoutReason()
	{
		return $this->userStorage->getLogoutReason();
	}

	/**
	 * @return IUserStorage original storage which is being decorated
	 */
	public function getInnerStorage()
	{
		return $this->userStorage;
	}

	/*** Nette\Http\UserStorage compliance - because Nette is fucked up and does not follow interfaces ***/

	/**
	 * Changes namespace; allows more users to share a session.
	 * @param  string
	 * @return self
	 */
	public function setNamespace($namespace)
	{
		if (!method_exists($this->userStorage, 'setNamespace')) {
			throw new \BadMethodCallException('Inner storage of type ' . get_class($this) . ' does not have setNamespace method.');
		}

		return $this->userStorage->setNamespace($namespace);
	}


	/**
	 * Returns current namespace.
	 * @return string
	 */
	public function getNamespace()
	{
		if (!method_exists($this->userStorage, 'getNamespace')) {
			throw new \BadMethodCallException('Inner storage of type ' . get_class($this) . ' does not have getNamespace method.');
		}

		return $this->userStorage->getNamespace();
	}
}
