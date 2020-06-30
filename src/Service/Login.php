<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Login
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function try($credentials)
    {
        $password = $credentials->password ?? null;

        if ($username = $credentials->username ?? false) {
            $entityManager = $this->entityManager;
            $userRepository = $entityManager->getRepository('App:User');
            $user = $userRepository->findOneBy(['username' => $username]);
        }

        if (!$user) {
            throw new \Exception(
                'No username found'
            );
            return false;
        }

        if ($user->getPassword() == $password) {
            $generator = new Generator();
            return $generator->accessToken($username);
        }

        return false;
    }
}
