<?php

namespace App\Controller\api;

use App\Repository\TrackerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class ChangelogController extends AbstractController
{
    /**
     * @Route("/changelog/{page}", name="changelog")
     */
    public function index($page, TrackerRepository $trackerRepository): Response
    {
        $changes = $trackerRepository->findAll();

        if (!$changes) {
            throw $this->createNotFoundException(
                'No changes found'
            );
        }

        // Pagination
        $adapter = new ArrayAdapter($changes);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setCurrentPage($page);

        return new Response($this->json($pagerfanta->getCurrentPageResults()), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
