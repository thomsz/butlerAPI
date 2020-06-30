<?php

namespace App\Service;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use App\Repository\TrackerRepository;

class Tracker
{
    private $trackerRepository;

    public function __construct(TrackerRepository $trackerRepository)
    {
        $this->trackerRepository = $trackerRepository;
    }

    public function Load($request)
    {
        $sortBy = $request->sort_by ?? 'id'; // id, userID, change, time
        $sortOrder = $request->order ?? 'ASC'; // ASC, DESC
        $page = $request->page ?? '1';

        $changes = $this->trackerRepository->findBy([], [$sortBy => $sortOrder]);

        if (!$changes) {
            throw new \Exception('No changes found');
        }

        // Pagination
        $adapter = new ArrayAdapter($changes);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setCurrentPage($page);

        return $pagerfanta->getCurrentPageResults();
    }
}
