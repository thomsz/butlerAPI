<?php

namespace App\Controller\api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\Login;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index(Login $login, Request $request, EntityManagerInterface $entityManager): Response
    {
        $credentials = json_decode($request->getContent());

        if ($jwt = $login->try($credentials)) {
            return new Response($this->json(['token' => $jwt]));
        } else {
            return new Response('Password is incorrect.', Response::HTTP_UNAUTHORIZED);
        }
    }
}
