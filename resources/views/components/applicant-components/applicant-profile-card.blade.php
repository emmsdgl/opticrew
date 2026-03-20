@props([
    'user',
    'totalApps'   => 0,
    'pendingApps' => 0,
    'hiredApps'   => 0,
])

<x-material-ui.profile-card
    :user="$user"
    :stats="[
        ['value' => $totalApps,  'label' => 'Applied',  'color' => 'text-gray-900 dark:text-white'],
        ['value' => $pendingApps,'label' => 'Pending',  'color' => 'text-yellow-500 dark:text-yellow-400'],
        ['value' => $hiredApps,  'label' => 'Hired',    'color' => 'text-green-500 dark:text-green-400'],
    ]"
    coverUploadRoute="{{ route('client.profile.upload-cover') }}"
/>
