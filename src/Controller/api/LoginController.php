<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use \Firebase\JWT\JWT;

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
            $key = '%env(JWT_PASSPHRASE)%';
            $payload = array(
                'iss' => '%env(URI)',
                'aud' => '%env(URI)',
                'iat' => time(),
                'nbf' => time() + 10,
                'exp' => time() + 3600,
            );

            $jwt = JWT::encode($payload, $key);

            return new Response($this->json($jwt));
        } else {
            return new Response('Password is incorrect.', Response::HTTP_UNAUTHORIZED);
        }
    }
}
