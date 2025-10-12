@props([
    'subHeader1' => '',
    'header1' => '',
    'subHeader2' => '',
])

<div id="step-content-container">
    <div id="text-container" class="w-full flex flex-col justify-center items-center mb-3">
        <p id="subheader1" class="w-full font-sans font-bold text-sm text-center dark:text-gray-500" 
           style="color: #0077FF;">
            {{ $subHeader1 }}
        </p>
        <h1 id="header1" class="w-full font-sans text-center font-bold text-5xl mb-5 mt-4 dark:text-gray-500" 
            style="color: #071957;">
            {{ $header1 }}
        </h1>
        <p id="subheader2" class="w-full font-sans font-normal text-sm text-justify dark:text-gray-500" 
           style="color: rgba(7, 24, 87, 0.5);">
            {{ $subHeader2 }}
        </p>
    </div>
</div>