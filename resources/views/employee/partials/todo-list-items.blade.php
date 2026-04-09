@props([
    'items' => [],
    'emptyTitle' => 'No tasks on this date',
    'emptyMessage' => 'There are no tasks scheduled for the selected date.',
])

<x-employee-components.task-overview-list
    :items="$items"
    fixedHeight="auto"
    maxHeight="100%"
    bgClass="bg-transparent"
    :emptyTitle="$emptyTitle"
    :emptyMessage="$emptyMessage" />
