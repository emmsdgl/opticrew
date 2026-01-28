{{-- resources/views/components/activity-card.blade.php --}}
@props([
    'icon' => '🧹',
    'title' => '',
    'date' => '',
    'price' => null,
    'status' => '',
    'statusColor' => 'text-green-600 dark:text-green-400',
])

<div
    {{ $attributes->merge([
        'class' =>
            'bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6
             hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200'
    ]) }}
>
    <div class="flex items-start gap-4">
        <!-- Icon -->
        <div
            class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30
                   rounded-lg flex items-center justify-center text-2xl">
            {{ $icon }}
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                {{ $title }}
            </h3>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                {{ $date }}
            </p>

            <!-- Actions Slot -->
            @if (isset($actions))
                <div class="flex flex-wrap gap-12">
                    {{ $actions }}
                </div>
            @endif
        </div>

        <!-- Meta -->
        <div class="flex-shrink-0 text-right">
            @if($price)
                <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                    {{ $price }}
                </div>
            @endif

            @if($status)
                <div class="text-sm font-medium {{ $statusColor }}">
                    {{ $status }}
                </div>
            @endif
        </div>
    </div>
</div>
