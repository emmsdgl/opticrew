@props([
    'headers' => [],
    'rows' => [],
    'emptyTitle' => 'No records found',
    'emptyMessage' => 'There are no records to display',
    'searchable' => true,
])

<div>
    @if(count($rows) > 0)
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block bg-white dark:bg-slate-900 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-slate-800">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="">
                        <tr class="border-b border-gray-200 dark:border-slate-800">
                            @foreach($headers as $header)
                                <th scope="col" class="px-6 py-4 text-{{ $header['align'] ?? 'left' }} text-xs font-semibold text-gray-600 dark:text-slate-400">
                                    {{ $header['label'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-800">
                        @foreach($rows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors"
                                @if(isset($row['attributes']))
                                    @foreach($row['attributes'] as $key => $value)
                                        {{ $key }}="{{ $value }}"
                                    @endforeach
                                @endif>
                                @foreach($row['columns'] as $columnIndex => $column)
                                    <td class="px-6 py-4 {{ $headers[$columnIndex]['align'] ?? 'text-left' }}">
                                        {!! $column !!}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Hidden on desktop) -->
        <div class="md:hidden space-y-3">
            @foreach($rows as $row)
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-slate-800"
                     @if(isset($row['attributes']))
                         @foreach($row['attributes'] as $key => $value)
                             {{ $key }}="{{ $value }}"
                         @endforeach
                     @endif>
                    @if(isset($row['mobile']))
                        {!! $row['mobile'] !!}
                    @else
                        <!-- Default mobile layout if not provided -->
                        <div class="space-y-2">
                            @foreach($row['columns'] as $columnIndex => $column)
                                @if(isset($headers[$columnIndex]))
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 dark:text-slate-400">{{ $headers[$columnIndex]['label'] }}</span>
                                        <div class="text-sm text-gray-900 dark:text-white">{!! $column !!}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow p-12 text-center border border-gray-200 dark:border-slate-800">
            <i class="fas fa-inbox text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
            <p class="text-lg font-medium text-gray-600 dark:text-gray-400">{{ $emptyTitle }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $emptyMessage }}</p>
        </div>
    @endif
</div>
