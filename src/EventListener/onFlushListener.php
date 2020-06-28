<?php

namespace App\EventListener;

use App\Entity\Customer;
use App\Entity\Tracker;
use Doctrine\ORM\Event\OnFlushEventArgs;

class FlushListener
{
    public function onFlush(OnFlushEventArgs $args)
    {

        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
        }

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $updatedEntity) {
        }
    }
}
