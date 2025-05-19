<?php

namespace App\Controller;

use App\Form\AdjustTimingType;
use App\Entity\TrackPoint;
use App\Message\RecalculateMessage;
use App\Repository\TrackPointRepository;
use App\Service\TrackAdjuster;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class TimingController extends AbstractController
{
    private MessageBusInterface $messageBus;
    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    #[Route('/adjust', name: 'adjust_timing')]
    public function adjust(Request $request, TrackAdjuster $trackAdjuster)
    {
        $form = $this->createForm(AdjustTimingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data['password'];
            $distance = $data['distance'];
            $newStartTime = $data['current_time'];

            if ($password !== 'travesia') {
                $this->addFlash('error', 'Contraseña incorrecta.');
                return $this->redirectToRoute('adjust_timing');
            }

            $dateStart = new \DateTimeImmutable($newStartTime->format('Y-m-d H:i:s'));

            $trackAdjuster->execute($distance, $dateStart);

//            $message = new RecalculateMessage(
//                $distance,
//                $dateStart->modify('-2 hours'),
//            );
//
//                $this->messageBus->dispatch($message);


            $this->addFlash('success', 'Horarios recalculados a partir de la nueva posición.');

            return $this->redirectToRoute('adjust_timing');
        }

        return $this->render('timing/adjust.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/points', name: 'points')]
    public function points(Request $request, TrackPointRepository $repository)
    {
        $points = $repository->findBy(
            [],[],10,0
        );

        return $this->json($points);

    }
}