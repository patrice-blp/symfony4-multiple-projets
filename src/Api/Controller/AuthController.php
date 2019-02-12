<?php

namespace App\Api\Controller;

use App\Api\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthController
 * @package App\Api\Controller
 */
class AuthController extends AbstractController
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \App\Api\Exception\BadRequestException
     * @throws \App\Api\Exception\UnAuthorizedException
     */
    public function login(Request $request)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $data = $this->userManager->login($username, $password);

        return $this->json($data);
    }
}
