@props([
    'columns' => [],
    'data' => [],
    'striped' => false,
    'hoverable' => true,
    'bordered' => false,
    'tableId' => null,
    'responsive' => true,
    'class' => ''
])

@php
    $tableId = $tableId ?? 'table-' . uniqid();
@endphp

<div {{ $attributes->merge(['class' => '' . $class]) }}>
    <div class="{{ $responsive ? 'w-full' : '' }}">
        <table class="min-w-full bg-white dark:bg-gray-800 {{ $bordered ? 'border border-gray-200 dark:border-gray-700' : '' }} rounded-lg" id="{{ $tableId }}">
            <!-- Table Header -->
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" 
                            class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wider {{ $column['headerClass'] ?? '' }}">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="{{ $striped ? 'divide-y divide-gray-200 dark:divide-gray-700' : '' }}">
                @forelse($data as $index => $row)
                    <tr class="
                        {{ $striped && $index % 2 === 0 ? 'bg-white dark:bg-gray-800' : '' }}
                        {{ $striped && $index % 2 !== 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}
                        {{ $hoverable ? 'hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150' : '' }}
                    ">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap {{ $column['cellClass'] ?? '' }}">
                                @if(isset($column['type']))
                                    @if($column['type'] === 'status')
                                        @php
                                            $value = data_get($row, $column['key']);
                                            $statusConfig = $column['statusConfig'][$value] ?? [
                                                'label' => $value,
                                                'bgColor' => 'bg-gray-100 dark:bg-gray-700',
                                                'textColor' => 'text-gray-800 dark:text-gray-200',
                                                'borderColor' => 'border-gray-300 dark:border-gray-600'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $statusConfig['bgColor'] }} {{ $statusConfig['textColor'] }} {{ $statusConfig['borderColor'] }}">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    @elseif($column['type'] === 'badge')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $column['badgeClass'] ?? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }}">
                                            {{ data_get($row, $column['key']) }}
                                        </span>
                                    @elseif($column['type'] === 'custom')
                                        {!! $column['render']($row, $index) !!}
                                    @else
                                        <span class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ data_get($row, $column['key']) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ data_get($row, $column['key']) }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@once
    @push('styles')
    <style>
        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .dark .overflow-x-auto::-webkit-scrollbar-track {
            background: #374151;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .dark .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #6b7280;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
    @endpush
@endonce