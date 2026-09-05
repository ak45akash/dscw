@props([
    'title' => 'Dashboard',
    'breadcrumb' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('dscw.business.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-graphite-100 text-graphite-900 dark:bg-graphite-950 dark:text-graphite-100">
    <div x-data="adminSidebar()" class="min-h-full lg:flex">
        @include('admin.partials.sidebar')

        <div class="flex min-h-full flex-1 flex-col lg:pl-72">
            @include('admin.partials.header', ['title' => $title, 'breadcrumb' => $breadcrumb])

            <main class="flex-1 p-4 sm:p-6">
                @if(session('success'))
                    <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
                @endif

                @if($errors->any())
                    <x-alert type="error" class="mb-6">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                {{ $slot }}
            </main>
        </div>

        <div
            x-show="open"
            x-transition.opacity
            @click="close()"
            class="fixed inset-0 z-40 bg-graphite-900/50 lg:hidden"
            style="display: none;"
        ></div>
    </div>
</body>
</html>
