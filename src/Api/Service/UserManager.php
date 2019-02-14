<?php

namespace App\Api\Service;

use App\Api\Exception\BadRequestException;
use App\Api\Exception\ConflictException;
use App\Api\Exception\NotFoundException;
use App\Api\Exception\UnAuthorizedException;
use App\Api\Model\User;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Cache\InvalidArgumentException;

/**
 * Class CacheManager
 * @package App\Api\Service
 */
class UserManager
{
    private const CACHE_STORE_NAME = 'api.cache';
    private const CACHE_USERS_DB_PREFIX = 'users._';

    /**
     * @var FilesystemAdapter
     */
    private $filesAdapter;

    /**
     * UserManager constructor.
     */
    public function __construct()
    {
        $this->filesAdapter = new FilesystemAdapter(self::CACHE_STORE_NAME);
    }

    /**
     * @param string $user
     * @param string $password
     * @return array
     *
     * @throws BadRequestException
     * @throws UnAuthorizedException
     */
    public function login(string $user, string $password)
    {
        $keyName = $this->getKeyName($user);

        if (!$user || !$password) {
            throw new BadRequestException('Bad credentials');
        }

        $hasItem = $this->filesAdapter->hasItem($keyName);

        if (!$hasItem) {
            throw new UnAuthorizedException('No user found');
        }

        try {
            $data = $this->filesAdapter->getItem($keyName)->get();
        } catch (InvalidArgumentException $exception) {
            throw new UnAuthorizedException('User not found with the data sent');
        }

        $isValidPassword = \password_verify($password, $data['password']);

        if (!$isValidPassword) {
            throw new UnAuthorizedException('Bad credentials');
        }

        $user = new User(...\array_values($data));
        return $user->getValues();
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $role
     * @return array
     *
     * @throws BadRequestException
     * @throws ConflictException
     */
    public function save(string $user, string $password, string $role): array
    {
        $keyName = $this->getKeyName($user);

        if (!$user || !$password || !$role) {
            throw new BadRequestException('username, password and roles fields are required');
        }

        $hasItem = $this->filesAdapter->hasItem($keyName);

        if ($hasItem) {
            throw new ConflictException('User already exists');
        }

        $data = [
            "username" => $user,
            "password" => \password_hash($password, PASSWORD_BCRYPT),
            "roles" => [$role]
        ];

        try {
            $item = $this->filesAdapter->getItem($keyName);
            $item->set($data);

            $isSaved = $this->filesAdapter->save($item);

            if (!$isSaved) {
                throw new BadRequestException('Failed to save user data');
            }
        } catch (InvalidArgumentException $exception) {
            throw new BadRequestException('Failed to save user data');
        }

        $user = new User(...\array_values($data));

        return $user->getValues();
    }

    /**
     * @param string $username
     * @return array
     *
     * @throws NotFoundException
     */
    public function listUserInfo(string $username): array
    {
        $keyName = $this->getKeyName($username);

        try {
            $item = $this->filesAdapter->getItem($keyName);
        } catch (InvalidArgumentException $exception) {
            throw new NotFoundException('Object not found');
        }

        $itemValue = $item->get();

        if (!$itemValue) {
            throw new NotFoundException('Object not found');
        }

        $user = new User(...\array_values($itemValue));

        return $user->getValues();
    }

    /**
     * @param string $key
     * @return bool
     *
     * @throws ConflictException
     */
    public function delete(string $key): bool
    {
        $keyName = $this->getKeyName($key);

        try {
            return $this->filesAdapter->delete($keyName);
        } catch (InvalidArgumentException $exception) {
            throw new ConflictException('Failed to delete user data');
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function getKeyName(string $key): string
    {
        return self::CACHE_USERS_DB_PREFIX . $key;
    }
}
