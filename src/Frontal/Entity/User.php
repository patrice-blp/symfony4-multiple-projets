<?php

namespace App\Frontal\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Frontal\Security
 */
class User implements UserInterface, EquatableInterface
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $salt;

    /** @var array */
    private $roles;

    /**
     * @param string $username
     * @param string $password
     * @param string $salt
     * @param array $roles
     */
    public function __construct(string $username, string $password, string $salt, array $roles)
    {
        /** @var string username */
        $this->username = $username;

        /** @var string password */
        $this->password = $password;

        /** @var string salt */
        $this->salt = $salt;

        /** @var array roles */
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
