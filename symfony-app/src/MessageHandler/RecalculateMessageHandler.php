<?php

namespace App\MessageHandler;

use App\Message\RecalculateMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\TrackAdjuster;

#[AsMessageHandler]
final class RecalculateMessageHandler
{
    public function __construct(private TrackAdjuster $trackAdjuster)
    {
    }

    public function __invoke(RecalculateMessage $message): void
    {
        $this->trackAdjuster->execute($message->distanceInMeters, $message->startTime);
    }
}