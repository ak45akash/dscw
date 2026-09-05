@props([
    'seoTitle' => null,
    'seoDescription' => null,
])

@php
    $business = $siteSettings['business'] ?? [];
    $themeMode = $siteSettings['theme']['mode'] ?? 'system';
    $businessName = $business['business_name'] ?? config('dscw.business.name');
    $metaTitle = $seoTitle ?? ($siteSettings['seo']['meta_title'] ?? $businessName);
    $metaDescription = $seoDescription ?? ($siteSettings['seo']['meta_description'] ?? '');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.theme.init('{{ $themeMode }}')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-gray-800 antialiased">
    @include('public.partials.header', ['businessName' => $businessName, 'themeMode' => $themeMode])

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('public.partials.footer', ['business' => $business, 'businessName' => $businessName])
</body>
</html>
