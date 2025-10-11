@props([
    'cards' => [],
    'date' => 'date',
    'type' => 'type',
    'price' => 'price',
    'location' => 'location',
])

    @foreach ($cards as $card)
        <x-taskcard 
            :startDate="$card[$date]"
            :serviceType="$card[$type]"
            :servicePrice="$card[$price]"
            :serviceLocation="$card[$location]"
        />
    @endforeach
