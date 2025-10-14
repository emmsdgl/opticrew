@props([
    'cards' => [],
    'date' => 'date',
    'type' => 'type',
    'price' => 'price',
    'location' => 'location',
])

<div class="flex flex-row overflow-x-scroll gap-3 w-full">
    @foreach ($cards as $card)
        <x-taskcard 
            :startDate="$card[$date]" 
            :serviceType="$card[$type]" 
            :servicePrice="$card[$price]" 
            :serviceLocation="$card[$location]" 
        />
    @endforeach
</div>