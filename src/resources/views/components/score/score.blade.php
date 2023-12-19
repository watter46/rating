<div class="w-full p-2 rounded-2xl bg-sky-950 h-1/3">
    <div class="bg-sky-900 rounded-2xl">
        <div class="flex items-center p-2">
            @if ($fixture['league']['img'])
                <img src="data:image/png;base64,<?= $fixture['league']['img'] ?>"
                    class="w-16 h-16 rounded-full cursor-pointer">
            @endif

            @unless($fixture['league']['img'])
                <div class="w-16 h-16 bg-gray-400 rounded-full cursor-pointer">
                </div>
            @endunless

            <div class="flex flex-col justify-center">
                <p class="text-2xl font-black" style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                    {{ $fixture['league']['league_name'] }}
                </p>
                
                <p class="font-black text-center" style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                    {{ $fixture['league']['round'] }}
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-around w-full">        
        @if ($fixture['home'])
            <div class="flex flex-col items-center p-2">
                @if ($fixture['home']['img'])
                    <img src="data:image/png;base64,<?= $fixture['home']['img'] ?>"
                        class="w-20 h-20 rounded-full cursor-pointer">
                @endif

                @unless($fixture['home']['img'])
                    <div class="w-20 h-20 bg-gray-400 rounded-full cursor-pointer">
                    </div>
                @endunless

                <p class="p-2 text-xl font-black text-center text-gray-300">{{ $fixture['home']['team_name'] }}</p>
            </div>
        @endif

        <div class="pt-2 text-5xl font-black text-gray-300">
            <div class="flex items-start text-center">
                <p class="p-2">{{ $fixture['home']['score'] }}</p>
                <p class="p-2">-</p>
                <p class="p-2">{{ $fixture['away']['score'] }}</p>
            </div>
            
            <div class="text-center">
                <p class="p-2 text-xl">Finished</p>
            </div>
        </div>

        @if ($fixture['away'])
            <div class="flex flex-col items-center p-2">
                @if ($fixture['away']['img'])
                    <img src="data:image/png;base64,<?= $fixture['away']['img'] ?>"
                        class="w-20 h-20 rounded-full cursor-pointer">
                @endif

                @unless($fixture['away']['img'])
                    <div class="w-20 h-20 bg-gray-400 rounded-full cursor-pointer">
                    </div>
                @endunless

                <p class="p-2 text-xl font-black text-center text-gray-300">{{ $fixture['away']['team_name'] }}</p>
            </div>
        @endif
    </div>
</div>