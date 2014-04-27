<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\Http;

use Doctrine\ORM\EntityManager;
use Majkl578\NetteAddons\Doctrine2Identity\Security\FakeIdentity;
use Nette\Object;
use Nette\Security\IUserStorage;
use Nette\Security\IIdentity;

/**
 * @author Michael Moravec
 */
class UserStorage extends Object implements IUserStorage
{
	/** @var IUserStorage */
	private $userStorage;

	/** @var EntityManager */
	private $entityManager;

	public function  __construct(IUserStorage $userStorage, EntityManager $entityManager) {
		$this->userStorage = $userStorage;
		$this->entityManager = $entityManager;
	}

	/**
	 * Sets the user identity.
	 * @return UserStorage Provides a fluent interface
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
	 * Returns current user identity, if any.
	 * @return IIdentity|NULL
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
	 * Sets the authenticated status of this user.
	 * @param  bool
	 * @return void
	 */
	function setAuthenticated($state)
	{
		return $this->userStorage->setAuthenticated($state);
	}

	/**
	 * Is this user authenticated?
	 * @return bool
	 */
	function isAuthenticated()
	{
		return $this->userStorage->isAuthenticated();
	}

	/**
	 * Enables log out from the persistent storage after inactivity.
	 * @param  string|int|DateTime number of seconds or timestamp
	 * @param  int Log out when the browser is closed | Clear the identity from persistent storage?
	 * @return void
	 */
	function setExpiration($time, $flags = 0)
	{
		return $this->userStorage->setExpiration($time, $flags);
	}

	/**
	 * Why was user logged out?
	 * @return int
	 */
	function getLogoutReason()
	{
		return $this->userStorage->getLogoutReason();
	}
}
