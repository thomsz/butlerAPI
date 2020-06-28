<?php

namespace App\Controller\api;

use App\Repository\TrackerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ChangelogController extends AbstractController
{
    /**
     * @Route("/changelog", name="changelog")
     */
    public function index(TrackerRepository $trackerRepository): Response
    {
        $changes = $trackerRepository->findAll();

        if (!$changes) {
            throw $this->createNotFoundException(
                'No changes found'
            );
        }

        return new Response($this->json($changes), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
