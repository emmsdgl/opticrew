@props([
    'title',
    'price',
    'description',
    'badges' => [],
    'bgImage' => null,
    'darkBgImage' => null,
    'gradient' => 'from-rose-400 via-pink-500 to-rose-600',
    'darkGradient' => 'from-rose-600 via-pink-700 to-rose-800',
    'overlayOpacity' => '40',
    'buttonText' => 'Book Now',
    'buttonUrl' => '#',
    'index' => 0
])

<div class="carousel-card group relative overflow-hidden rounded-3xl cursor-pointer transition-all duration-500 ease-out"
    data-card="{{ $index }}" style="flex: 1; min-width: 100px;">
    
    @if($bgImage)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-opacity duration-300 dark:opacity-0"
            style="background-image: url('{{ $bgImage }}');">
        </div>

        @if($darkBgImage)
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-0 transition-opacity duration-300 dark:opacity-100"
                style="background-image: url('{{ $darkBgImage }}');">
            </div>
        @else
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-0 transition-opacity duration-300 dark:opacity-100"
                style="background-image: url('{{ $bgImage }}'); filter: brightness(0.6) contrast(1.2);">
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} dark:{{ $darkGradient }} opacity-{{ $overlayOpacity }} dark:opacity-{{ $overlayOpacity }} transition-all duration-500"></div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} dark:{{ $darkGradient }} transition-all duration-500"></div>
    @endif
    
    <div class="absolute inset-0 bg-black/20 dark:bg-black/40 transition-all duration-500"></div>
    
    <div class="relative h-full flex flex-col justify-end p-6 md:p-8 z-10">
                
        <div class="card-expanded-content opacity-0 transition-all duration-500 space-y-4">

        <div class="space-y-2">
                <p class="text-white/80 text-base"><i class="fa-solid fa-location-dot"></i></p>
                <p class="text-white font-bold text-3xl drop-shadow-lg">{{ $price }}</p>
            </div>
            
            <h3 class="text-white font-bold text-3xl md:text-4xl drop-shadow-lg">
                {{ $title }}
            </h3>
            
            <p class="text-white/90 text-base leading-relaxed drop-shadow">
                {!! $description !!}
            </p>
            
            @if(count($badges) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($badges as $badge)
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm border border-white/30">
                            {{ $badge }}
                        </span>
                    @endforeach
                </div>
            @endif
            
        </div>
    </div>
</div>