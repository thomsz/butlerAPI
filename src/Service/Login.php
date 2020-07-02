<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class Login
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function try($credentials)
    {

        if ($username = $credentials->username ?? false) {
            $entityManager = $this->entityManager;
            $userRepository = $entityManager->getRepository('App:User');
            $user = $userRepository->findOneBy(['username' => $username]);
        } else {
            return ['message' => 'Please provide a username'];
        }

        if (!$user) {
            return ['message' => 'Username was not found'];
        }

        $password = $credentials->password ?? null;

        if (!$password) {
            return ['message' => 'Please provide a password'];
        } else if ($user->getPassword() == $password) {
            $generator = new Generator();
            return $generator->accessToken($username);
        }

        return ['message' => 'Credentials are incorrect'];
    }
}
