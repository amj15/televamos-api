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
        $conn = $this->em->getConnection();

        $sql = <<<SQL
        UPDATE track_point
        SET estimated_time = :referenceTime::timestamp + ((distance - :referenceDistance) / :speed) * INTERVAL '1 second'
        WHERE distance >= :referenceDistance
    SQL;

        $conn->executeStatement($sql, [
            'referenceTime' => $referenceTime->format('Y-m-d H:i:s'), // sin zona horaria
            'referenceDistance' => $distanceInMeters,
            'speed' => 2.77777778, // 10 km/h en m/s
        ]);
    }

}