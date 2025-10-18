<?php

namespace App\Services\Optimization\GeneticAlgorithm;

use Illuminate\Support\Collection;

class Population
{
    protected Collection $individuals;
    protected int $maxSize;

    public function __construct(int $maxSize)
    {
        $this->maxSize = $maxSize;
        $this->individuals = collect();
    }

    public function addIndividual(Individual $individual): void
    {
        if ($this->individuals->count() < $this->maxSize) {
            $this->individuals->push($individual);
        }
    }

    public function evaluateFitness(FitnessCalculator $calculator, array $teamEfficiencies): void
    {
        foreach ($this->individuals as $individual) {
            $fitness = $calculator->calculate($individual, $teamEfficiencies);
            $individual->setFitness($fitness);
        }

        $this->individuals = $this->individuals->sortByDesc(fn($ind) => $ind->getFitness());
    }

    public function getBest(): Individual
    {
        return $this->individuals->first();
    }

    public function getRandomIndividuals(int $count): Collection
    {
        return $this->individuals->random(min($count, $this->individuals->count()));
    }

    public function size(): int
    {
        return $this->individuals->count();
    }
}