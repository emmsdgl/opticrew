@props([
    'label' => '',
    'icon'  => null,
    'color' => 'blue',
])

@php
$palette = [
    'blue'   => ['light' => 'bg-blue-50 border-blue-500/40 text-blue-700',        'dark' => 'dark:bg-blue-950/40 dark:border-blue-400/40 dark:text-blue-300',        'glow' => '96,165,250'],
    'yellow' => ['light' => 'bg-yellow-50 border-yellow-500/40 text-yellow-700',  'dark' => 'dark:bg-yellow-950/40 dark:border-yellow-400/40 dark:text-yellow-300',  'glow' => '234,179,8'],
    'green'  => ['light' => 'bg-green-50 border-green-500/40 text-green-700',     'dark' => 'dark:bg-green-950/40 dark:border-green-400/40 dark:text-green-300',     'glow' => '34,197,94'],
    'purple' => ['light' => 'bg-purple-50 border-purple-500/40 text-purple-700',  'dark' => 'dark:bg-purple-950/40 dark:border-purple-400/40 dark:text-purple-300',  'glow' => '168,85,247'],
    'red'    => ['light' => 'bg-red-50 border-red-500/40 text-red-700',           'dark' => 'dark:bg-red-950/40 dark:border-red-400/40 dark:text-red-300',           'glow' => '239,68,68'],
    'gray'   => ['light' => 'bg-gray-50 border-gray-400/40 text-gray-600',        'dark' => 'dark:bg-gray-800/40 dark:border-gray-500/40 dark:text-gray-400',        'glow' => '107,114,128'],
    'indigo' => ['light' => 'bg-indigo-50 border-indigo-500/40 text-indigo-700',  'dark' => 'dark:bg-indigo-950/40 dark:border-indigo-400/40 dark:text-indigo-300',  'glow' => '99,102,241'],
];
$c = $palette[$color] ?? $palette['blue'];
@endphp

<span
    {{ $attributes->merge(['class' => 'x-badge inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded border ' . $c['light'] . ' ' . $c['dark']]) }}
    style="--badge-rgb: {{ $c['glow'] }};">
    @if($icon)
        <i class="fa-solid {{ $icon }} text-xs"></i>
    @endif
    {{ $label }}
</span>
