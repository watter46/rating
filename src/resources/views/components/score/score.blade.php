<div {{ $attributes->merge(['class' => 'w-full p-2']) }}>
    <div class="rounded-2xl">
        <div class="flex items-center justify-start p-2">
            <div class="flex items-center justify-center gap-x-2">
                @if ($league['img'])
                    <img src="{{ $league['img'] }}" class="w-5 h-5 bg-pink-500 rounded-full md:w-7 md:h-7">
                @endif

                @unless($league['img'])
                    <div class="w-5 h-5 bg-gray-400 md:w-7 md:h-7"></div>
                @endunless
                
                <p class="text-sm font-black text-gray-300 md:text-base">
                    {{ $league['name'] }}
                </p>
                
                <p class="text-sm font-black text-center text-gray-300 md:text-base">
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
                        class="w-12 h-12 md:w-16 md:h-16">
                @endif

                @unless($teams['home']['img'])
                    <div class="w-12 h-12 bg-gray-400 md:w-16 md:h-16"></div>
                @endunless

                <p class="p-2 text-sm font-black text-center text-gray-300 md:text-xl">{{ $teams['home']['name'] }}</p>
            </div>
        @endif

        <div class="w-1/3 pt-2">
            <div class="flex items-start justify-center">
                <p class="p-2 text-xl font-black text-gray-300 md:text-3xl">{{ $score['fulltime']['home'] }}</p>
                <p class="p-2 text-xl font-black text-gray-300 md:text-3xl">-</p>
                <p class="p-2 text-xl font-black text-gray-300 md:text-3xl">{{ $score['fulltime']['away'] }}</p>
            </div>
            
            <p class="p-2 text-base font-black text-center text-gray-300 md:text-xl">Finished</p>
        </div>

        @if ($teams['away'])
            <div class="flex flex-col items-center w-1/3 p-2">
                @if ($teams['away']['img'])
                    <img src="{{ $teams['away']['img'] }}"
                        class="w-12 h-12 md:w-16 md:h-16">
                @endif

                @unless($teams['away']['img'])
                    <div class="w-12 h-12 bg-gray-400 md:w-16 md:h-16">
                    </div>
                @endunless

                <p class="p-2 text-sm font-black text-center text-gray-300 md:text-xl">{{ $teams['away']['name'] }}</p>
            </div>
        @endif
    </div>
</div>