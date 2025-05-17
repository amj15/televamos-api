<?php

namespace App\Service;

use App\Entity\TrackPoint;
use Doctrine\ORM\EntityManagerInterface;

class TrackAdjuster
{
    private EntityManagerInterface $em;
    private const SPEED_MS = 2.77778;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function execute(int $distanceInMeters, \DateTimeImmutable $referenceTime): void
    {
        $trackPoints = $this->em->getRepository(TrackPoint::class)
            ->createQueryBuilder('tp')
            ->where('tp.distance >= :dist')
            ->setParameter('dist', $distanceInMeters)
            ->orderBy('tp.distance', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($trackPoints as $trackPoint) {
            \assert($trackPoint instanceof TrackPoint);
            $diffMeters = $trackPoint->distance() - $distanceInMeters;
            $seconds = (int) round($diffMeters / self::SPEED_MS);
            $newTime = (new \DateTimeImmutable())->setTimestamp($referenceTime->getTimestamp() + $seconds);
            $trackPoint->setEstimatedTime($newTime);
            $this->em->flush();
            $this->em->clear();
        }
    }

}