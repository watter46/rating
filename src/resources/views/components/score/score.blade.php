<div {{ $attributes->merge(['class' => 'w-full p-2 rounded-2xl bg-sky-950']) }}>
    <div class="bg-sky-900 rounded-2xl">
        <div class="flex items-center p-2">
            @if ($league['img'])
                <img src="{{ $league['img'] }}"
                    class="w-16 h-16">
            @endif

            @unless($league['img'])
                <div class="w-16 h-16 bg-gray-400">
                </div>
            @endunless

            <div class="flex flex-col justify-center">
                <p class="text-2xl font-black" style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                    {{ $league['name'] }}
                </p>
                
                <p class="font-black text-center" style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                    {{ $league['round'] }}
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-around w-full">        
        @if ($teams['home'])
            <div class="flex flex-col items-center w-1/3 p-2">
                @if ($teams['home']['img'])
                    <img src="{{ $teams['home']['img'] }}"
                        class="w-20 h-20">
                @endif

                @unless($teams['home']['img'])
                    <div class="w-20 h-20 bg-gray-400">
                    </div>
                @endunless

                <p class="p-2 text-xl font-black text-center text-gray-300">{{ $teams['home']['name'] }}</p>
            </div>
        @endif

        <div class="w-1/3 pt-2 text-5xl font-black text-gray-300">
            <div class="flex items-start justify-center">
                <p class="p-2">{{ $score['fulltime']['home'] }}</p>
                <p class="p-2">-</p>
                <p class="p-2">{{ $score['fulltime']['away'] }}</p>
            </div>
            
            <div class="text-center">
                <p class="p-2 text-xl">Finished</p>
            </div>
        </div>

        @if ($teams['away'])
            <div class="flex flex-col items-center w-1/3 p-2">
                @if ($teams['away']['img'])
                    <img src="{{ $teams['away']['img'] }}"
                        class="w-20 h-20">
                @endif

                @unless($teams['away']['img'])
                    <div class="w-20 h-20 bg-gray-400">
                    </div>
                @endunless

                <p class="p-2 text-xl font-black text-center text-gray-300">{{ $teams['away']['name'] }}</p>
            </div>
        @endif
    </div>
</div>