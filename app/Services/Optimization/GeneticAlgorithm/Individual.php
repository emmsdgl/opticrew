<?php

namespace App\Services\Optimization\GeneticAlgorithm;

class Individual
{
    protected array $schedule;
    protected ?float $fitness = null;
    protected array $metadata = []; // ✅ Add this

    public function __construct(array $schedule)
    {
        $this->schedule = $schedule;
    }

    public function getSchedule(): array
    {
        return $this->schedule;
    }

    public function setFitness(float $fitness): void
    {
        $this->fitness = $fitness;
    }

    public function getFitness(): ?float
    {
        return $this->fitness;
    }

    public function setSchedule(array $schedule): void
    {
        $this->schedule = $schedule;
    }
    
    // ✅ Add metadata methods
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getMetadata(string $key = null)
    {
        if ($key === null) {
            return $this->metadata;
        }
        return $this->metadata[$key] ?? null;
    }
}