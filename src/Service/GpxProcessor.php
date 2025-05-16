<?php

namespace App\Service;

use App\Entity\TrackPoint;
use Doctrine\ORM\EntityManagerInterface;

class GpxProcessor
{
    private EntityManagerInterface $em;
    private const SPEED_MS = 2.77778;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function process(string $filePath, \DateTimeImmutable $startTime): void
    {
        $xml = simplexml_load_file($filePath);
        $trackPoints = $xml->trk->trkseg->trkpt;

        $prevLat = null;
        $prevLon = null;
        $totalDistance = 0;
        $batchSize = 100;
        $i = 0;

        $this->em->getConnection()->executeStatement('TRUNCATE TABLE track_point RESTART IDENTITY CASCADE');

        foreach ($trackPoints as $trackPoint) {
            $lat = (float) $trackPoint['lat'];
            $lon = (float) $trackPoint['lon'];

            if ($prevLat !== null && $prevLon !== null) {
                $segmentDistance = $this->haversineDistance($prevLat, $prevLon, $lat, $lon);
                $totalDistance += $segmentDistance;
            }



            $diffSeconds = \intval($totalDistance / self::SPEED_MS);
            $estimatedTime = (new \DateTimeImmutable())->setTimestamp($startTime->getTimestamp() + $diffSeconds);

            $domainTrackPoint = new TrackPoint(
                latitude: $lat,
                longitude: $lon,
                distance: $totalDistance,
                estimatedTime: $estimatedTime,
            );

            $this->em->persist($domainTrackPoint);

            $prevLat = $lat;
            $prevLon = $lon;

            if (++$i % $batchSize === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}