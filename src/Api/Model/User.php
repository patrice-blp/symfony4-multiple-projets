<?php

namespace App\Api\Model;

/**
 * Class User
 * @package App\Api\Model
 */
class User implements ValueObjectInterface
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var array */
    private $roles;

    /**
     * User constructor.
     *
     * @param string $username
     * @param string $password
     * @param array $roles
     */
    public function __construct(string $username, string $password, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $objects = \get_object_vars($this);
        unset($objects['password']);

        return $objects;
    }
}
