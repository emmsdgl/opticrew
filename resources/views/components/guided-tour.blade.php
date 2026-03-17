@props([
    'tourName' => '',
    'steps' => '[]',
    'autoStart' => true,
])

@php
    $user = auth()->user();
    $toursCompleted = $user->tours_completed ?? [];
    $hasCompleted = in_array($tourName, $toursCompleted);
@endphp

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css"/>
<style>
    /* Custom Driver.js theme matching OptiCrew's design */
    .driver-popover {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: 1px solid #e5e7eb;
        max-width: 380px;
        padding: 0;
        font-family: 'Familjen Grotesk', system-ui, sans-serif;
    }

    .dark .driver-popover {
        background: #1e293b;
        border-color: #334155;
        color: #e2e8f0;
    }

    .driver-popover .driver-popover-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        padding: 20px 24px 4px;
        margin: 0;
    }

    .dark .driver-popover .driver-popover-title {
        color: #f1f5f9;
    }

    .driver-popover .driver-popover-description {
        font-size: 0.9rem;
        color: #64748b;
        padding: 8px 24px 16px;
        line-height: 1.5;
        margin: 0;
    }

    .dark .driver-popover .driver-popover-description {
        color: #94a3b8;
    }

    .driver-popover .driver-popover-footer {
        padding: 12px 24px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .driver-popover .driver-popover-progress-text {
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .driver-popover .driver-popover-navigation-btns {
        display: flex;
        gap: 8px;
    }

    .driver-popover .driver-popover-prev-btn {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-shadow: none;
    }

    .driver-popover .driver-popover-prev-btn:hover {
        background: #e2e8f0;
    }

    .dark .driver-popover .driver-popover-prev-btn {
        background: #334155;
        color: #cbd5e1;
        border-color: #475569;
    }

    .driver-popover .driver-popover-next-btn,
    .driver-popover .driver-popover-close-btn-text {
        background: #2A6DFA;
        color: #ffffff;
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-shadow: none;
    }

    .driver-popover .driver-popover-next-btn:hover,
    .driver-popover .driver-popover-close-btn-text:hover {
        background: #1d5fd8;
    }

    .driver-popover .driver-popover-close-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 16px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .driver-popover .driver-popover-close-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .dark .driver-popover .driver-popover-close-btn {
        background: #334155;
        color: #94a3b8;
    }

    .driver-popover-arrow {
        border: 1px solid #e5e7eb;
    }

    .dark .driver-popover-arrow {
        border-color: #334155;
    }

    /* Welcome overlay for first step */
    .tour-welcome-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #2A6DFA 0%, #1d5fd8 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    .tour-welcome-icon svg {
        width: 32px;
        height: 32px;
        color: white;
    }

    /* Step indicator dots */
    .tour-step-dots {
        display: flex;
        gap: 6px;
        justify-content: center;
        padding: 8px 0;
    }

    .tour-step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        transition: all 0.3s;
    }

    .tour-step-dot.active {
        background: #2A6DFA;
        width: 24px;
        border-radius: 4px;
    }
</style>
@endpush
@endonce

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tourName = @js($tourName);
    const hasCompleted = @js($hasCompleted);
    const autoStart = @js($autoStart);
    const tourSteps = @js(json_decode($steps, true));

    if (!tourSteps || tourSteps.length === 0) return;

    // Build Driver.js steps
    const driverSteps = tourSteps.map((step, index) => ({
        element: step.element || undefined,
        popover: {
            title: step.title,
            description: step.description,
            side: step.side || 'bottom',
            align: step.align || 'start',
        }
    }));

    // Track if user clicked the "Got it!" button
    let clickedDoneBtn = false;

    // Initialize Driver.js
    const driverObj = window.driver.js.driver({
        showProgress: true,
        animate: true,
        smoothScroll: true,
        stagePadding: 8,
        stageRadius: 12,
        allowClose: true,
        overlayColor: 'rgba(0, 0, 0, 0.6)',
        popoverClass: 'opticrew-tour',
        progressText: 'Step @{{current}} of @{{total}}',
        nextBtnText: 'Next',
        prevBtnText: 'Back',
        doneBtnText: 'Got it!',
        steps: driverSteps,
        onNextClick: () => {
            // If on the last step, the "Next" button becomes "Got it!"
            if (driverObj.isLastStep()) {
                clickedDoneBtn = true;
            }
            driverObj.moveNext();
        },
        onDestroyStarted: () => {
            // Only mark as completed if user actually clicked "Got it!"
            if (clickedDoneBtn) {
                fetch('{{ route("tour.complete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ tour: tourName })
                });
            }
            clickedDoneBtn = false;
            driverObj.destroy();
        }
    });

    // Store driver instance globally so replay button can access it
    window.__opticrewTours = window.__opticrewTours || {};
    window.__opticrewTours[tourName] = driverObj;

    // Auto-start if user hasn't completed this tour yet
    if (autoStart && !hasCompleted) {
        setTimeout(() => {
            driverObj.drive();
        }, 800);
    }

    // Listen for replay event
    document.addEventListener('replay-tour', function(e) {
        if (e.detail === tourName || !e.detail) {
            driverObj.drive();
        }
    });
});
</script>
@endpush
