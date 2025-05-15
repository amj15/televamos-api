<?php

namespace App\Controller;

use App\Entity\TrackPoint;
use App\Repository\TrackPointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        return new JsonResponse(['is' => 'me']);
    }

    #[Route('/position', name: 'track_segment', methods: ['GET'])]
    public function getTrackSegment(Request $request, TrackPointRepository $repository): JsonResponse
    {
        $timeParam = $request->query->get('time');
        $offset = (int) $request->query->get('offset', 0); // en minutos

        if (!$timeParam) {
            return new JsonResponse(['error' => 'Missing "time" parameter'], 400);
        }

        // Hora enviada por el cliente (ej. en su hora local)
        $localTime = new \DateTimeImmutable($timeParam);

        // Convertimos a UTC restando el offset
        $utcTime = $localTime->modify("-{$offset} minutes");
        $utcEndTime = $utcTime->modify('+60 seconds');

        $points = $repository->findBetweenTimes($utcTime, $utcEndTime);

        if (0 === count($points)) {
            return new JsonResponse(['error' => 'No points found'], 404);
        }

        $resultPoint = [];
        if (1 === \count($points)){
            $point = $points[0];
            \assert($point instanceof TrackPoint);

            for($i=0; $i <= 60; $i++){
                $resultPoint[] = $point->jsonSerialize();
            }

            if($point->id() === 1 && $point->estimatedTime() > $utcTime) {
                $status = 'not_started';
            }else{
                if($point->estimatedTime() > $utcTime) {
                    $status = 'running';
                }else{
                    $status = 'past_point';
                }
            }
        }else{
            $pointIndex = 0;
            for($i=0; $i <= 60; $i++){
                $point = $points[$pointIndex];
                \assert($point instanceof TrackPoint);

                if($point->estimatedTime() > $utcTime) {
                    $pointIndex++;
                }

                if($pointIndex >= \count($points)) {
                    $pointIndex = \count($points) - 1;
                }

                $resultPoint[] = $point->jsonSerialize();
            }
            $status = 'running';
        }

        return new JsonResponse(
            [
                'points' => $resultPoint,
                'status' => $status,
            ]
        );
    }
}