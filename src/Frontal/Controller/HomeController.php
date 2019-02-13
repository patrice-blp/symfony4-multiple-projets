<?php

namespace App\Frontal\Controller;

use App\Frontal\Service\UserClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package App\Frontal\Controller
 */
class HomeController extends AbstractController
{
    /** @var UserClient  */
    private $userClient;

    /**
     * HomeController constructor.
     * @param UserClient $userClient
     */
    public function __construct(
        UserClient $userClient
    )
    {
        $this->userClient = $userClient;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {
        if (null === $this->getUser()) {
            return $this->redirectToRoute('frontal.login');
        }

        if ($request->isMethod('POST')) {
            $result = $this->userClient->addUser(
                $request->request->get('username'),
                $request->request->get('password'),
                $request->request->get('role')
            );

            if ($result) {
                $this->addFlash('warning', $request->request->get('username') . '\'s account has been created');

                return $this->redirectToRoute('frontal.index');
            }

            $this->addFlash('warning', 'An error occurred while saving the user');
        }

        return $this->render('default.html.twig');
    }

    /**
     * @return Response
     */
    public function pageOne(): Response {
        return $this->render('pages/page-index.html.twig');
    }

    /**
     * @return Response
     */
    public function pageTwo(): Response {
        return $this->render('pages/page-index.html.twig');
    }

    /**
     * @return Response
     */
    public function pageThree(): Response {
        return $this->render('pages/page-index.html.twig');
    }
}