<?php

namespace App\Controller\api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\Login;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $credentials = json_decode($request->getContent());
        $login = new Login($entityManager);

        if ($jwt = $login->try($credentials)) {
            return new Response($this->json(['token' => $jwt]));
        } else {
            return new Response('Password is incorrect.', Response::HTTP_UNAUTHORIZED);
        }
    }
}
