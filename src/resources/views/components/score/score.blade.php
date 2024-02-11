<div {{ $attributes->merge(['class' => 'w-full p-2']) }}>
    <div class="rounded-2xl">
        <div class="flex items-center justify-start p-2">
            <div class="flex justify-center text-sm gap-x-2">
                @if ($league['img'])
                    <img src="{{ $league['img'] }}" class="w-5 h-5 bg-pink-500 rounded-xl">
                @endif

                @unless($league['img'])
                    <div class="w-5 h-5 bg-gray-400"></div>
                @endunless
                
                <p class="font-black text-gray-300">
                    {{ $league['name'] }}
                </p>
                
                <p class="font-black text-center text-gray-300">
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
                        class="w-16 h-16">
                @endif

                @unless($teams['home']['img'])
                    <div class="w-16 h-16 bg-gray-400">
                    </div>
                @endunless

                <p class="p-2 text-lg font-black text-center text-gray-300">{{ $teams['home']['name'] }}</p>
            </div>
        @endif

        <div class="w-1/3 pt-2 text-3xl font-black text-gray-300">
            <div class="flex items-start justify-center">
                <p class="p-2">{{ $score['fulltime']['home'] }}</p>
                <p class="p-2">-</p>
                <p class="p-2">{{ $score['fulltime']['away'] }}</p>
            </div>
            
            <div class="text-center">
                <p class="p-2 text-lg">Finished</p>
            </div>
        </div>

        @if ($teams['away'])
            <div class="flex flex-col items-center w-1/3 p-2">
                @if ($teams['away']['img'])
                    <img src="{{ $teams['away']['img'] }}"
                        class="w-16 h-16">
                @endif

                @unless($teams['away']['img'])
                    <div class="w-16 h-16 bg-gray-400">
                    </div>
                @endunless

                <p class="p-2 text-lg font-black text-center text-gray-300">{{ $teams['away']['name'] }}</p>
            </div>
        @endif
    </div>
</div>