<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('client.dashboard')],
            ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => route('client.appointments')],
            ['label' => 'Pricing', 'icon' => 'fa-file-lines', 'href' => route('client.pricing')],
            ['label' => 'Feedbacks', 'icon' => 'fa-chart-line', 'href' => route('client.feedback')]
        ];

        $teams = []; // No teams for client sidebar
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
            {{ $slot }}
    </section>
    @stack('scripts')

</x-layouts.general-dashboard>