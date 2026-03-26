@props(['settings', 'siteName'])

<footer class="bg-dark-900 border-t border-white/5 mt-auto relative z-10">
    <div class="mx-auto max-w-7xl px-6 py-6 md:flex md:items-center md:justify-between lg:px-8">
        <div class="mt-8 md:order-1 md:mt-0">
            <p class="text-center text-xs leading-5 text-gray-400">
                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
            </p>
            <p class="text-center md:text-left text-xs text-gray-600 mt-2">
                Not affiliated with Mojang AB.
            </p>
        </div>
        <div class="flex justify-center space-x-6 md:order-2">
            @if($settings?->social_media)
                @foreach($settings->social_media as $platform => $link)
                    <a href="{{ $link }}" target="_blank" class="text-gray-400 hover:text-primary transition-colors">
                        <span class="text-sm font-semibold">{{ $platform }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</footer>