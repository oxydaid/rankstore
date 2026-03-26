<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="dark">

    {{-- SEO & Open Graph --}}
    {{-- Variabel $siteName, $logo, dll sekarang sudah dikirim otomatis oleh Provider --}}
    <title>{{ $title ?? $siteName }}</title>
    <meta name="description" content="{{ $description ?? ($settings?->site_description ?? 'Top Up Rank Minecraft Server Termurah & Terpercaya.') }}">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? $siteName }}">
    <meta property="og:description" content="{{ $description ?? ($settings?->site_description ?? '') }}">
    <meta property="og:image" content="{{ $image ?? $logo }}">
    <meta property="twitter:card" content="summary_large_image">

    <!-- Font -->
    <!-- Google Fonts -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Russo+One&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Russo+One&display=swap" rel="stylesheet">

    {{-- Favicon --}}
    @if($settings?->favicon)
        <link rel="icon" href="{{ asset('img/'.$settings->favicon) }}">
    @endif

    {{-- Styles & Scripts --}}
    <style>
        :root {
            /* Variabel ini dikirim dari Provider */
            --primary: {{ $primary }};
            --secondary: {{ $secondary }};
        }

        /* Critical baseline style to reduce visual flash before Vite CSS fully applies */
        html,
        body {
            background: #0f1016;
            color: #fff;
            font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #0f1016; }
        ::-webkit-scrollbar-thumb { background: #26293b; border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        [x-cloak],
        [wire\:cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-dark-900 text-white min-h-screen flex flex-col">
    <div class="fixed inset-0 z-[-1] opacity-30 pointer-events-none" 
         style="background-image: url('{{ asset('img/'.$settings?->hero_background ?? '') }}'); background-size: cover; background-position: center;">
    </div>
    <!-- Overlay Gradient -->
    <div class="fixed inset-0 z-[-1] bg-linear-to-b from-dark-900/70 via-dark-900/90 to-dark-900 pointer-events-none"></div>
    
<x-navbar :logo="$logo" :site-name="$siteName" />
    <main class="grow relative z-10">
        {{ $slot }}
    </main>
<x-footer :settings="$settings" :site-name="$siteName" />
</body>
</html>