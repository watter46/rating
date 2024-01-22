<div class="w-full p-2 rounded-2xl bg-sky-950">
    <div class="flex items-center w-full h-full cursor-pointer"
        wire:click="toFixture">        
        <div class="flex items-center justify-center w-1/4 h-full px-2 py-1 font-black text-gray-300 border-r border-gray-500">
            {{ $score['fixture']['date'] }}
        </div>

        <div class="flex flex-col justify-center w-2/4 px-2 py-1">
            @if ($score['teams']['home'])
                <div class="flex items-center pl-2">
                    @if ($score['teams']['home']['img'])
                        <img src="{{ $score['teams']['home']['img'] }}"
                            class="w-8 h-8">
                    @endif

                    @unless($score['teams']['home']['img'])
                        <div class="w-8 h-8 bg-gray-400">
                        </div>
                    @endunless

                    <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">{{ $score['teams']['home']['name'] }}</p>
                </div>
            @endif

            @if ($score['teams']['away'])
                <div class="flex items-center pl-2">
                    @if ($score['teams']['away']['img'])
                        <img src="{{ $score['teams']['away']['img'] }}"
                            class="w-8 h-8">
                    @endif

                    @unless($score['teams']['away']['img'])
                        <div class="w-8 h-8 bg-gray-400">
                        </div>
                    @endunless

                    <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                        {{ $score['teams']['away']['name'] }}
                    </p>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center w-1/4 rounded-lg bg-sky-900" title="{{ $score['league']['round'] }}">
            <div class="flex flex-col items-center p-2">
                <img src="{{ $score['league']['img'] }}"
                        class="w-10 h-10">

                <div class="flex justify-center p-2">
                    <p class="font-black" style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                        {{ $score['league']['name'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>