@extends('components.layouts.general-landing')

@section('title', __('landing.castcrew.page_title'))

@push('styles')
    <style>
        /* Ensure proper background for both modes */
        body {
            background-image: none;
        }

        .light-mode-bg {
            background-color: #ffffff;
        }

        .dark .light-mode-bg {
            background-color: #111827;
        }
    </style>
@endpush

@section('content')
    <!-- CASTCREW OVERVIEW -->
    <section class="system light-mode-bg py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base/7 font-semibold text-blue-600 dark:text-blue-400">{{ __('landing.castcrew.introducing') }}</h2>
                <p
                    class="mt-2 text-4xl font-semibold text-pretty text-gray-900 dark:text-white sm:text-5xl lg:text-balance">
                    {{ __('landing.castcrew.headline_prefix') }} <span class="font-normal">{{ __('landing.castcrew.headline_suffix') }}</span>
                </p>
                <p class="mt-6 text-base text-justify text-gray-600 dark:text-gray-300">
                    {{ __('landing.castcrew.overview') }}
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                    <div class="relative pl-16">
                        <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                            <div
                                class="absolute top-0 left-0 flex size-10 items-center justify-center rounded-lg bg-blue-600 dark:bg-blue-500">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                    <path
                                        d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            {{ __('landing.castcrew.feature_1_title') }}
                        </dt>
                        <dd class="mt-2 text-base/7 text-gray-600 dark:text-gray-400">
                            {{ __('landing.castcrew.feature_1_desc') }}
                        </dd>
                    </div>
                    <div class="relative pl-16">
                        <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                            <div
                                class="absolute top-0 left-0 flex size-10 items-center justify-center rounded-lg bg-blue-600 dark:bg-blue-500">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                    <path
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            {{ __('landing.castcrew.feature_2_title') }}
                        </dt>
                        <dd class="mt-2 text-base/7 text-gray-600 dark:text-gray-400">
                            {{ __('landing.castcrew.feature_2_desc') }}
                        </dd>
                    </div>
                    <div class="relative pl-16">
                        <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                            <div
                                class="absolute top-0 left-0 flex size-10 items-center justify-center rounded-lg bg-blue-600 dark:bg-blue-500">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                    <path
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            {{ __('landing.castcrew.feature_3_title') }}
                        </dt>
                        <dd class="mt-2 text-base/7 text-gray-600 dark:text-gray-400">
                            {{ __('landing.castcrew.feature_3_desc') }}
                        </dd>
                    </div>
                    <div class="relative pl-16">
                        <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                            <div
                                class="absolute top-0 left-0 flex size-10 items-center justify-center rounded-lg bg-blue-600 dark:bg-blue-500">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                    <path
                                        d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            {{ __('landing.castcrew.feature_4_title') }}
                        </dt>
                        <dd class="mt-2 text-base/7 text-gray-600 dark:text-gray-400">
                            {{ __('landing.castcrew.feature_4_desc') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- MEET THE DEVELOPERS -->
    <section class="developers light-mode-bg py-24 sm:py-32">
        <div class="mx-auto grid max-w-7xl gap-20 px-6 lg:px-8 xl:grid-cols-3">
            <div class="max-w-xl">
                <h2 class="text-3xl font-semibold text-pretty text-gray-900 dark:text-white sm:text-4xl">
                    {{ __('landing.castcrew.team_title') }}
                </h2>
                <p class="mt-6 text-base text-gray-600 dark:text-gray-400 text-justify">
                    {{ __('landing.castcrew.team_desc') }} </p>
            </div>
            <ul role="list" class="grid gap-x-8 gap-y-12 sm:grid-cols-2 sm:gap-y-16 xl:col-span-2">
                <li>
                    <div class="flex items-center gap-x-6">
                        <img src="/images/people/Balona.svg" alt=""
                            class="size-16 object-cover scale-125 rounded-full ring-2 ring-gray-200 dark:ring-white/10" />
                        <div>
                            <h3 class="text-base/7 font-semibold tracking-tight text-gray-900 dark:text-white">Adam Jay B.
                            </h3>
                            <p class="text-sm/6 font-semibold text-blue-600 dark:text-blue-400">{{ __('landing.castcrew.role_ba') }}</p>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="flex items-center gap-x-6">
                        <img src="/images/people/Esteban.svg" alt=""
                            class="size-16 object-cover scale-125 rounded-full ring-2 ring-gray-200 dark:ring-white/10" />
                        <div>
                            <h3 class="text-base/7 font-semibold tracking-tight text-gray-900 dark:text-white">Sushmita E.
                            </h3>
                            <p class="text-sm/6 font-semibold text-blue-600 dark:text-blue-400">{{ __('landing.castcrew.role_tester') }}</p>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="flex items-center gap-x-6">
                        <img src="/images/people/Digol.svg"
                            alt="" class="size-16 object-cover scale-125 rounded-full ring-2 ring-gray-200 dark:ring-white/10" />
                        <div>
                            <h3 class="text-base/7 font-semibold tracking-tight text-gray-900 dark:text-white">Emmaus D.
                            </h3>
                            <p class="text-sm/6 font-semibold text-blue-600 dark:text-blue-400">{{ __('landing.castcrew.role_backend') }}</p>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="flex items-center gap-x-6">
                        <img src="/images/people/San Buenaventura.svg"
                            alt="" class="size-16 object-cover scale-125 rounded-full ring-2 ring-gray-200 dark:ring-white/10" />
                        <div>
                            <h3 class="text-base/7 font-semibold tracking-tight text-gray-900 dark:text-white">Leiramarie S.
                            </h3>
                            <p class="text-sm/6 font-semibold text-blue-600 dark:text-blue-400">{{ __('landing.castcrew.role_frontend') }}</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </section>
@endsection