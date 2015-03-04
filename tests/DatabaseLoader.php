<?php

namespace Majkl578\NetteAddons\Doctrine2Identity\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Majkl578\NetteAddons\Doctrine2Identity\Tests\Entities\User;

class DatabaseLoader
{
	/** @var bool */
	private $isDbPrepared = FALSE;

	/** @var Connection */
	private $connection;

	/** @var EntityManager */
	private $entityManager;

	public function __construct(Connection $connection, EntityManager $entityManager)
	{
		$this->connection = $connection;
		$this->entityManager = $entityManager;
	}

	public function loadUserTableWithOneItem()
	{
		if ($this->isDbPrepared) {
			return;
		}

		$this->connection->query('CREATE TABLE user (id INTEGER NOT NULL, name string, PRIMARY KEY(id))');

		$user = new User('John');
		$this->entityManager->persist($user);
		$this->entityManager->flush();

		$this->isDbPrepared = TRUE;
	}

}
