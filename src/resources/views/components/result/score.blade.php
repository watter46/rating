<div {{ $attributes->merge(['class' => 'w-full p-1']) }}>
    <div class="flex items-center justify-center w-full">        
        @if ($teams['home'])
            <div class="flex items-center justify-end h-full">
                <p class="p-2 text-sm font-black text-center text-gray-300">{{ $teams['home']['name'] }}</p>
                
                @if ($teams['home']['img'])
                    <img src="{{ $teams['home']['img'] }}"
                        class="w-8 h-8">
                @endif

                @unless($teams['home']['img'])
                    <div class="w-8 h-8 bg-gray-400">
                    </div>
                @endunless
            </div>
        @endif

        <div class="w-1/5 text-xl font-black text-gray-300">
            <div class="flex items-start justify-center">
                <p class="p-1">{{ $score['fulltime']['home'] }}</p>
                <p class="p-1">-</p>
                <p class="p-1">{{ $score['fulltime']['away'] }}</p>
            </div>
        </div>

        @if ($teams['away'])
            <div class="flex items-center justify-start h-full">
                @if ($teams['away']['img'])
                    <img src="{{ $teams['away']['img'] }}"
                        class="w-8 h-8">
                @endif

                @unless($teams['away']['img'])
                    <div class="w-8 h-8 bg-gray-400">
                    </div>
                @endunless

                <p class="p-2 text-sm font-black text-center text-gray-300">{{ $teams['away']['name'] }}</p>
            </div>
        @endif
    </div>
</div>