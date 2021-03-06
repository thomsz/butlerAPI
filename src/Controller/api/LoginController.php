<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Login;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function index(Login $login, Request $request): Response
    {
        $credentials = json_decode($request->getContent());

        $jwt = $login->try($credentials);

        if (is_string($jwt)) {
            return new Response($this->json(['token' => $jwt]));
        } else {
            return new Response($this->json($jwt), Response::HTTP_UNAUTHORIZED);
        }
    }
}
