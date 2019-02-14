<?php

namespace App\Api\Controller;

use App\Api\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserManagerController
 * @package App\Api\Controller
 */
class UserManagerController extends AbstractController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * AuthController constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws \App\Api\Exception\BadRequestException
     * @throws \App\Api\Exception\ConflictException
     */
    public function createUser(Request $request): JsonResponse
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $role = $request->request->get('role');

        $user = $this->userManager->save($username, $password, $role);

        return $this->json($user);
    }

    /**
     * @param string $username
     * @return JsonResponse
     *
     * @throws \App\Api\Exception\NotFoundException
     */
    public function userInfo(string $username): JsonResponse
    {
        $user = $this->userManager->listUserInfo($username);

        return $this->json($user);
    }
}
