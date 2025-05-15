<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TrackPoint implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'float')]
    private float $latitude;

    #[ORM\Column(type: 'float')]
    private float $longitude;

    #[ORM\Column(type: 'float')]
    private float $distance = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $estimatedTime;

    public function id(): int
    {
        return $this->id;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function distance(): float
    {
        return $this->distance;
    }

    public function estimatedTime(): \DateTimeImmutable
    {
        return $this->estimatedTime;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function setDistance(float $distance): void
    {
        $this->distance = $distance;
    }

    public function setEstimatedTime(\DateTimeImmutable $estimatedTime): void
    {
        $this->estimatedTime = $estimatedTime;
    }

    public function __construct(float $latitude, float $longitude, float $distance, \DateTimeImmutable $estimatedTime)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->distance = $distance;
        $this->estimatedTime = $estimatedTime;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance' => $this->distance,
            'estimatedTime' => $this->estimatedTime->format(\DateTimeInterface::RFC3339),
        ];
    }
}