<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $credentials = json_decode($request->getContent());

        $password = $credentials->password ?? null;

        if ($username = $credentials->username ?? false) {
            $user = $userRepository->findOneByUsername($username);
        }

        if (!$user) {
            throw $this->createNotFoundException(
                'No username found'
            );
        }

        if ($user->getPassword() == $password) {
            return new Response('Welcome');
        } else {
            return new Response('Password is incorrect.', Response::HTTP_UNAUTHORIZED);
        }
    }
}
