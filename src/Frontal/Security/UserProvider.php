<?php

namespace App\Frontal\Security;

use App\Frontal\Entity\User;
use App\Frontal\Service\UserClient;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package App\Frontal\Security
 */
class UserProvider implements UserProviderInterface
{
    /** @var UserClient */
    private $userClient;

    /**
     * UserProvider constructor.
     * @param UserClient $userClient
     */
    public function __construct(UserClient $userClient)
    {
        $this->userClient = $userClient;
    }

    /**
     * @param string $username
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->userClient->loadUserByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return new User($user['username'], '', '', $user['roles']);
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        if (!$user->getUsername()) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}