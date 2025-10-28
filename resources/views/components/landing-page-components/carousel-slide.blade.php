@props([
    'title',
    'subtitle' => null,
    'description',
    'buttonText' => 'Learn More',
    'buttonUrl' => '#',
    'badges' => [],
    'price' => null,
    'backgroundImage' => null,
    'textColor' => 'text-white',
    'darkTextColor' => 'text-white',
    'isActive' => false,
    'index' => 0
])

<div class="carousel-item {{ $isActive ? 'active' : '' }} absolute inset-0 transition-opacity duration-700 ease-in-out {{ $isActive ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
    <div class="relative w-full h-full flex flex-col justify-center items-center text-center px-8 md:px-16">
        
        @if($backgroundImage)
            <div class="absolute inset-0 w-full h-full">
                <img src="{{ $backgroundImage }}" 
                     alt="Background for {{ $title }}" 
                     class="w-full h-full object-cover">
            </div>
            <div class="absolute inset-0 bg-black/20 dark:bg-black/40"></div>
        @endif

        <div class="relative z-10">
            @if($subtitle)
                <p class="text-sm {{ $textColor }}/80 dark:{{ $darkTextColor }}/70 mb-4 font-medium tracking-wide">
                    {{ $subtitle }}
                </p>
            @endif

            @if($price)
                <p class="text-sm {{ $textColor === 'text-white' ? 'text-white/80' : 'text-gray-800' }} dark:{{ $darkTextColor === 'text-white' ? 'text-white/80' : 'text-gray-200' }} mb-4">
                    Estimated Price Range: <span class="font-bold">{{ $price }}</span>
                </p>
            @endif

            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold {{ $textColor }} dark:{{ $darkTextColor }} leading-tight mb-6 max-w-3xl">
                {{ $title }}
            </h2>

            @if(count($badges) > 0)
                <div class="flex flex-wrap justify-center gap-2 mb-6 max-w-2xl">
                    @foreach($badges as $badge)
                        <span class="bg-white/20 dark:bg-gray-800/60 backdrop-blur-sm text-gray-900 dark:text-gray-100 text-xs px-4 py-2 rounded-full border border-white/40 dark:border-gray-700/40">
                            {{ $badge }}
                        </span>
                    @endforeach
                </div>
            @endif

            <p class="text-base md:text-lg {{ $textColor === 'text-white' ? 'text-white/90' : 'text-gray-800' }} dark:{{ $darkTextColor === 'text-white' ? 'text-white/80' : 'text-gray-200' }} max-w-2xl mb-8">
                {!! $description !!}
            </p>

            <a href="{{ $buttonUrl }}"
                class="inline-block {{ $textColor === 'text-white' ? 'bg-white/20 dark:bg-white/10 backdrop-blur-sm text-white hover:bg-white/30 dark:hover:bg-white/20 border border-white/30 dark:border-white/20' : 'bg-gray-900 dark:bg-blue-600 text-white hover:bg-gray-800 dark:hover:bg-blue-700 shadow-lg' }} font-normal px-8 py-3 rounded-full transition-all duration-300">
                {{ $buttonText }}
            </a>
        </div>
    </div>
</div>