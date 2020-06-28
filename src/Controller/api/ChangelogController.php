<?php

namespace App\Controller\api;

use App\Repository\TrackerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class ChangelogController extends AbstractController
{
    /**
     * @Route("/changelog", name="changelog")
     */
    public function index(TrackerRepository $trackerRepository, Request $request): Response
    {
        $request = json_decode($request->getContent());

        $sortBy = $request->sort_by ?? 'id'; // id, userID, change, time
        $sortOrder = $request->order ?? 'ASC'; // ASC, DESC
        $page = $request->page ?? '1';

        $changes = $trackerRepository->findBy([], [$sortBy => $sortOrder]);

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
