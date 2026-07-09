<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CourierDB') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex">

            {{-- Left brand panel --}}
            <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-indigo-900 to-slate-900 flex-col justify-between p-12">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-lg">CourierDB</span>
                </a>
                <div>
                    <blockquote class="text-white">
                        <p class="text-2xl font-semibold leading-relaxed mb-4">
                            &ldquo;Delivering trust,<br/>one parcel at a time.&rdquo;
                        </p>
                        <p class="text-indigo-300 text-sm">Courier Parcel Tracking &amp; Delivery Management System</p>
                    </blockquote>
                </div>
                <div class="text-indigo-400 text-xs">Database Management Systems Lab Project</div>
            </div>

            {{-- Right form panel --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center bg-slate-50 p-8">
                <div class="w-full max-w-md">
                    <a href="/" class="flex items-center gap-2 mb-8 lg:hidden">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-slate-900 text-lg">CourierDB</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>
