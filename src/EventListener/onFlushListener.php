<?php

namespace App\EventListener;

use App\Entity\Customer;
use App\Entity\Tracker;
use Doctrine\ORM\Event;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Serializer;

class FlushListener
{
    public function onFlush(Event\OnFlushEventArgs $args)
    {

        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof Customer) {
                $tracker = new Tracker();
                $tracker->setUserID($entity->getId());
                $tracker->setChange('delete');
                $tracker->setContent(['Customer was deleted.']);
                $tracker->setTime(new \Datetime());

                $entityManager->persist($tracker);
                $metaData = $entityManager->getClassMetadata('App\Entity\Tracker');
                $unitOfWork->computeChangeSet($metaData, $tracker);
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Customer) {

                $changeSet = $unitOfWork->getEntityChangeSet($entity);
                $trackChange = [];

                foreach ($changeSet as $change) {
                    $previousValueForField = array_key_exists(0, $change) ? $change[0] : null;
                    $newValueForField = array_key_exists(1, $change) ? $change[1] : null;

                    if ($previousValueForField != $newValueForField) {
                        $trackChange[] = ['from' => $previousValueForField, 'to' => $newValueForField];
                    }
                }

                $tracker = new Tracker();
                $tracker->setUserID($entity->getId());
                $tracker->setChange('update');
                $tracker->setContent($trackChange);
                $tracker->setTime(new \Datetime());

                $entityManager->persist($tracker);
                $metaData = $entityManager->getClassMetadata('App\Entity\Tracker');
                $unitOfWork->computeChangeSet($metaData, $tracker);
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Customer) {
            $normalizer = [new Serializer\Normalizer\ObjectNormalizer()];
            $serializer = new Serializer\Serializer($normalizer);

            $entityManager = $args->getObjectManager();

            $customer = $entityManager->getRepository('App:Customer')->find($entity->getId());

            $tracker = new Tracker();
            $tracker->setUserID($entity->getId());
            $tracker->setChange('create');
            $tracker->setContent([$serializer->normalize($customer, null)]);
            $tracker->setTime(new \Datetime());

            $entityManager->persist($tracker);
            $entityManager->flush();
        }
    }
}
