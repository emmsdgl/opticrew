@props([
    'startDate' => null,
    'serviceType' => null,
    'servicePrice' => null,
    'serviceLocation' => null,
])

<a href="#"
   class="flex flex-col min-w-[15rem] max-w-[15rem] h-auto p-6 
          bg-white border border-gray-200 rounded-lg shadow-sm 
          hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 
          dark:hover:bg-gray-700 transition-all duration-200 ease-in-out">

    <p id="layer-1" class="font-semibold text-sm mb-3 text-gray-700 dark:text-gray-400">
        {{ $startDate }} 
    </p>

    <h5 id="layer-2" class="mb-1 text-lg font-bold text-gray-900 dark:text-white">
        {{ $serviceType }} 
    </h5>

    <div id="layer-4" class="flex flex-row w-full justify-between">
        <div class="flex flex-col">
            <p id="price" class="font-normal text-sm text-gray-700 dark:text-white">{{ $servicePrice }} </p>
            <p id="location" class="font-normal text-sm text-gray-700 dark:text-gray-400">{{ $serviceLocation }}  </p>
        </div>
        </div>
</a>