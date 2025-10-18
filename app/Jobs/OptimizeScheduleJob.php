<?php

namespace App\Jobs;

use App\Services\Optimization\OptimizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OptimizeScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected string $serviceDate;
    protected array $locationIds;
    protected ?int $triggeredByTaskId;

    public function __construct(string $serviceDate, array $locationIds = [], ?int $triggeredByTaskId = null)
    {
        $this->serviceDate = $serviceDate;
        $this->locationIds = $locationIds;
        $this->triggeredByTaskId = $triggeredByTaskId;
    }

    public function handle(OptimizationService $optimizationService): void
    {
        Log::info('Starting background schedule optimization', [
            'service_date' => $this->serviceDate,
        ]);

        try {
            $result = $optimizationService->optimizeSchedule(
                $this->serviceDate,
                $this->locationIds,
                $this->triggeredByTaskId
            );

            Log::info('Background optimization completed', [
                'service_date' => $this->serviceDate,
                'status' => $result['status'],
            ]);

            // Optionally trigger notifications or events
            // event(new ScheduleOptimizationCompleted($result));

        } catch (\Exception $e) {
            Log::error('Background optimization failed', [
                'service_date' => $this->serviceDate,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Optimization job failed permanently', [
            'service_date' => $this->serviceDate,
            'error' => $exception->getMessage(),
        ]);
    }
}