<?php

namespace App\Repository;

use App\Entity\TrackPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrackPoint>
 */
class TrackPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackPoint::class);
    }

    public function findBetweenTimes(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $currentPoints = $this->createQueryBuilder('t')
            ->where('t.estimatedTime BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('t.distance', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (0 !== count($currentPoints)) {
            return $currentPoints;
        }

        $currentPoints =  $this->createQueryBuilder('t')
            ->where('t.estimatedTime < :start')
            ->setParameter('start', $start)
            ->orderBy('t.distance', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (0 !== count($currentPoints)) {
            return $currentPoints;
        }

        return $this->createQueryBuilder('t')
            ->orderBy('t.estimatedTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}