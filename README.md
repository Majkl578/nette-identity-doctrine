# Nette 2 & Doctrine 2 entities

Nette addon for using Doctrine 2 entities directly as Nette identity


## Motivation

If you are using Nette 2 and Doctrine 2 together, you will sooner or later
want to use some of your entities also as identity, because it is practical.

Fortunately, this addon is here to help you with this task!


## Requirements

- PHP 5.3.2+
- Nette 2.2+
- Doctrine ORM 2.3+


## Installation

1. Install via composer:

   `composer require majkl578/nette-identity-doctrine`

2. Register extension in your configuration file in extensions section:
    ```yaml
    extensions:
        doctrine2identity: Majkl578\NetteAddons\Doctrine2Identity\DI\IdentityExtension
    ```

3. Delete cache.

You're done. ;)


## Usage

Imagine you have an application where you're implementing authentication.
All you have is a regular entity for the user in your application.
Now you want to implement the authentication itself - you need to have an identity.
The only thing you have to do is to implement `Nette\Security\IIdentity` on your user entity,
so you get something like this:

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserEntity implements IIdentity
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	private $id;

	/** @ORM\Column */
	private $name;

	... other properties, getters & setters

	/* implementation of IIdentity */
	public function getId()
	{
		return $this->id;
	}

	public function getRoles()
	{
		return array();
	}
}
```

(Notice the empty getRoles() method. That is due to the requirement of IIdentity.
If you don't use authorization, unfortunately, it can't be omitted, sorry.)

That's all. Everything is automatic!
And you can even use entities with composite identifiers!


## Issues

In case of any problems, just leave an issue here on GitHub (or, better, send a pull request).
