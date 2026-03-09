<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('applicant.dashboard')],
        ];
    @endphp
    <x-sidebar :navOptions="$navOptions" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
        {{ $slot }}
    </section>
    @stack('scripts')

</x-layouts.general-dashboard>
