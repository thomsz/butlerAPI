<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Tracker;

class ChangelogController extends AbstractController
{
    /**
     * @Route("/changelog", name="changelog", methods={"GET"})
     */
    public function index(Tracker $tracker, Request $request): Response
    {
        $request = json_decode($request->getContent());

        return new Response($this->json($tracker->load($request)), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
