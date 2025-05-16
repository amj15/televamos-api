<?php

namespace App\Controller;

use App\Form\AdjustTimingType;
use App\Entity\TrackPoint;
use App\Message\RecalculateMessage;
use App\Repository\TrackPointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function adjust(Request $request)
    {
        $form = $this->createForm(AdjustTimingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $distance = $data['distance'];
            $newStartTime = $data['current_time'];

            $message = new RecalculateMessage(
                $distance,
                new \DateTimeImmutable($newStartTime->format('Y-m-d H:i:s')),
            );

            $this->messageBus->dispatch($message);


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