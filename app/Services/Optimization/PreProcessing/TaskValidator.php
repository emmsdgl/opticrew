<?php

namespace App\Services\Optimization\PreProcessing;

use Illuminate\Support\Collection;

class TaskValidator
{
    public function validate(Collection $tasks, array $constraints): array
    {
        $valid = collect();
        $invalid = collect();

        foreach ($tasks as $task) {
            // Get coordinates from contracted_client via location relationship
            $latitude = $task->location?->contractedClient?->latitude ?? null;
            $longitude = $task->location?->contractedClient?->longitude ?? null;

            \Log::info("Validating task", [
                'id' => $task->id,
                'location_id' => $task->location_id,
                'client_latitude' => $latitude,
                'client_longitude' => $longitude,
                'scheduled_date' => $task->scheduled_date,
                'duration' => $task->duration,
                'travel_time' => $task->travel_time
            ]);

            if ($this->isValid($task, $constraints)) {
                \Log::info("Task PASSED validation", ['id' => $task->id]);
                $valid->push($task);
            } else {
                $reason = $this->getInvalidReason($task, $constraints);
                \Log::error("Task FAILED validation", [
                    'id' => $task->id,
                    'reason' => $reason
                ]);
                $invalid->push([
                    'task' => $task,
                    'reason' => $reason,
                ]);
            }
        }

        \Log::info("Validation complete", [
            'valid_count' => $valid->count(),
            'invalid_count' => $invalid->count()
        ]);

        return [
            'valid' => $valid,
            'invalid' => $invalid,
        ];
    }

    protected function isValid($task, array $constraints): bool
    {
        // Rule 2.1: Location accessibility
        if (!$this->isLocationAccessible($task)) {
            return false;
        }

        // Rule 2.2: Time window constraints
        if (!$this->isWithinTimeWindow($task, $constraints)) {
            return false;
        }

        // Rule 2.3: Equipment availability
        if (!$this->hasRequiredEquipment($task)) {
            return false;
        }

        return true;
    }

    protected function isLocationAccessible($task): bool
    {
        // Check if task has location with contracted client coordinates
        if (empty($task->location_id)) {
            return false;
        }

        // Get coordinates from contracted_client via location relationship
        $latitude = $task->location?->contractedClient?->latitude ?? null;
        $longitude = $task->location?->contractedClient?->longitude ?? null;

        return !empty($latitude) && !empty($longitude);
    }

    protected function isWithinTimeWindow($task, array $constraints): bool
    {
        $taskTime = strtotime($task->scheduled_time);
        $workStart = strtotime($constraints['work_start_time'] ?? '08:00:00');
        $workEnd = strtotime($constraints['work_end_time'] ?? '18:00:00');

        return $taskTime >= $workStart && $taskTime <= $workEnd;
    }

    protected function hasRequiredEquipment($task): bool
    {
        // Check if required equipment is available
        if (empty($task->required_equipment)) {
            return true;
        }

        // Implement equipment availability check
        return true; // Placeholder
    }

    protected function getInvalidReason($task, array $constraints): string
    {
        if (!$this->isLocationAccessible($task)) {
            return 'Location not accessible';
        }
        if (!$this->isWithinTimeWindow($task, $constraints)) {
            return 'Outside time window';
        }
        if (!$this->hasRequiredEquipment($task)) {
            return 'Required equipment unavailable';
        }
        return 'Unknown reason';
    }
}