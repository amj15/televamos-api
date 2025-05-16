<?php

namespace App\Controller;

use App\Entity\TrackPoint;
use App\Repository\TrackPointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/public')]
class PublicController extends AbstractController
{
    #[Route('/track/geojson', name: 'track_geojson')]
    public function geojson(TrackPointRepository $repository): JsonResponse
    {
        $points = $repository->findBy([], ['id' => 'ASC']);

        $coordinates = array_map(function (TrackPoint $point) {
            return [$point->longitude(), $point->latitude()];
        }, $points);

        $geojson = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => $coordinates,
            ],
            'properties' => new \stdClass(),
        ];

        return new JsonResponse($geojson);
    }

    #[Route('/map', name: 'map_view')]
    public function viewMap(TrackPointRepository $repository): Response
    {
        $points = $repository->findBy([], ['estimatedTime' => 'ASC']);
        return $this->render('timing/map.html.twig', [
            'points' => $points,
        ]);
    }
}