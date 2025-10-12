@props([
    'subHeader1' => '',
    'header1' => '',
    'subHeader2' => '',
])

<div id="step-content-container">
    <div id="text-container" class="w-full flex flex-col">
        <p id="subheader1" class="text-[#0077FF] dark:text-gray-500 w-full font-sans font-bold text-sm text-justify">
            {{ $subHeader1 }}
        </p>
        <h1 id="header1" class="text-[#071957] dark:text-gray-500 w-full font-sans font-bold text-xl">{{ $header1 }}</p>
            <p id="subheader2"
                class="text-[#07185780] dark:text-gray-500 w-full font-sans font-bold text-sm text-justify">
                {{ $subHeader2 }}
            </p>
    </div>