<?php

namespace App\Message;

final class RecalculateMessage
{
    public function __construct(
        public readonly int $distanceInMeters,
        public readonly \DateTimeImmutable $startTime
    ) {}
}