<?php

namespace App\Services\Team;

use Illuminate\Support\Collection;

class TeamEfficiencyCalculator
{
    protected const BASE_EFFICIENCY = 1.0;
    protected const SYNERGY_BONUS = 0.15; // 15% bonus for good team composition
    protected const SIZE_PENALTY_FACTOR = 0.05; // 5% penalty per person over optimal

    /**
     * Calculate efficiency multiplier for each team
     * Returns array of efficiency values indexed by team position
     */
    public function calculate(Collection $teams): array
    {
        $efficiencies = [];
        
        foreach ($teams as $index => $team) {
            $efficiencies[$index] = $this->calculateTeamEfficiency($team);
        }
        
        return $efficiencies;
    }

    /**
     * Calculate efficiency for a single team
     */
    public function calculateTeamEfficiency(Collection $team): float
    {
        if ($team->isEmpty()) {
            return self::BASE_EFFICIENCY;
        }
        
        // 1. Base efficiency (average of individual efficiencies)
        $avgEfficiency = $team->avg('efficiency') ?? self::BASE_EFFICIENCY;
        
        // 2. Skill diversity bonus
        $skillDiversityBonus = $this->calculateSkillDiversityBonus($team);
        
        // 3. Experience synergy
        $experienceSynergy = $this->calculateExperienceSynergy($team);
        
        // 4. Team size adjustment
        $sizeAdjustment = $this->calculateSizeAdjustment($team->count());
        
        // Combine factors
        $totalEfficiency = $avgEfficiency * 
            (1 + $skillDiversityBonus) * 
            (1 + $experienceSynergy) * 
            $sizeAdjustment;
        
        // Cap between 0.5 and 2.0
        return max(0.5, min(2.0, $totalEfficiency));
    }

    /**
     * Calculate bonus based on skill diversity in team
     */
    protected function calculateSkillDiversityBonus(Collection $team): float
    {
        $allSkills = $team->flatMap(fn($member) => $member->skills ?? [])->unique();
        $teamSize = $team->count();
        
        if ($teamSize === 0) {
            return 0;
        }
        
        // More unique skills = better bonus (up to SYNERGY_BONUS)
        $skillsPerMember = $allSkills->count() / $teamSize;
        return min(self::SYNERGY_BONUS, $skillsPerMember * 0.05);
    }

    /**
     * Calculate synergy bonus based on experience mix
     */
    protected function calculateExperienceSynergy(Collection $team): float
    {
        $experiences = $team->pluck('years_of_experience')->filter();
        
        if ($experiences->isEmpty()) {
            return 0;
        }
        
        $avgExperience = $experiences->avg();
        $hasJunior = $experiences->min() < 2;
        $hasSenior = $experiences->max() > 5;
        
        // Bonus for having both junior and senior members (mentoring effect)
        if ($hasJunior && $hasSenior) {
            return self::SYNERGY_BONUS * 0.5;
        }
        
        // Small bonus for experienced teams
        if ($avgExperience > 5) {
            return self::SYNERGY_BONUS * 0.3;
        }
        
        return 0;
    }

    /**
     * Adjust efficiency based on team size
     */
    protected function calculateSizeAdjustment(int $teamSize): float
    {
        $optimalSize = 3;
        
        if ($teamSize === $optimalSize) {
            return 1.0; // No adjustment
        }
        
        // Penalty for teams that are too large (coordination overhead)
        if ($teamSize > $optimalSize) {
            $excess = $teamSize - $optimalSize;
            return 1.0 - ($excess * self::SIZE_PENALTY_FACTOR);
        }
        
        // Slight penalty for teams that are too small
        if ($teamSize < $optimalSize) {
            return 1.0 - (($optimalSize - $teamSize) * self::SIZE_PENALTY_FACTOR * 0.5);
        }
        
        return 1.0;
    }

    /**
     * Get detailed efficiency breakdown for reporting
     */
    public function getEfficiencyBreakdown(Collection $team): array
    {
        return [
            'base_efficiency' => $team->avg('efficiency') ?? self::BASE_EFFICIENCY,
            'skill_diversity_bonus' => $this->calculateSkillDiversityBonus($team),
            'experience_synergy' => $this->calculateExperienceSynergy($team),
            'size_adjustment' => $this->calculateSizeAdjustment($team->count()),
            'final_efficiency' => $this->calculateTeamEfficiency($team),
            'team_size' => $team->count(),
            'unique_skills' => $team->flatMap(fn($m) => $m->skills ?? [])->unique()->count(),
        ];
    }
}