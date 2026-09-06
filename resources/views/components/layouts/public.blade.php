@props([
    'seoTitle' => null,
    'seoDescription' => null,
    'seoImage' => null,
    'canonical' => null,
    'ogType' => 'website',
])

@php
    $business = $siteSettings['business'] ?? [];
    $themeMode = $siteSettings['theme']['mode'] ?? 'system';
    $businessName = $business['business_name'] ?? config('dscw.business.name');
    $metaTitle = $seoTitle ?? ($siteSettings['seo']['meta_title'] ?? $businessName);
    $metaDescription = $seoDescription ?? ($siteSettings['seo']['meta_description'] ?? '');
    $canonicalUrl = $canonical ?? url()->current();
    $ogImage = $seoImage ? (str_starts_with($seoImage, 'http') ? $seoImage : asset($seoImage)) : asset('images/hero.jpg');
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
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $businessName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    @if($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    @if($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="theme-color" content="#0e7490">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'AutoWash',
            'name' => $businessName,
            'url' => url('/'),
            'image' => $ogImage,
            'telephone' => $business['phone'] ?? null,
            'email' => $business['email'] ?? null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business['address'] ?? null,
                'addressLocality' => $business['city'] ?? 'Sahibzada Ajit Singh Nagar',
                'addressRegion' => 'Punjab',
                'addressCountry' => 'IN',
            ],
            'areaServed' => ['Sahibzada Ajit Singh Nagar', 'Matour', 'Punjab'],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script>
        (function () {
            try {
                var pref = localStorage.getItem('dscw-theme') || 'system';
                var admin = @json($themeMode);
                var dark = false;
                if (admin !== 'disabled') {
                    if (pref === 'dark') dark = true;
                    else if (pref === 'light') dark = false;
                    else dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            } catch (e) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-graphite-800 antialiased dark:bg-graphite-950 dark:text-graphite-100">
    @include('public.partials.header', ['businessName' => $businessName, 'themeMode' => $themeMode])

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('public.partials.footer', ['business' => $business, 'businessName' => $businessName])
</body>
</html>
