<div {{ $attributes->merge(['class' => 'w-full p-2']) }}>    
    <div class="rounded-2xl">
        <div class="flex items-center justify-start p-2">
            <div class="flex items-center justify-center gap-x-2">
                @if ($leagueData['img'])
                    <img src="{{ asset($leagueData['img']) }}" class="w-5 h-5 bg-pink-500 rounded-full md:w-7 md:h-7">
                @endif

                @unless($leagueData['img'])
                    <div class="w-5 h-5 bg-gray-400 md:w-7 md:h-7"></div>
                @endunless
                
                <p class="text-sm font-black text-gray-300 md:text-base">
                    {{ $leagueData['name'] }}
                </p>
                
                <p class="text-sm font-black text-center text-gray-300 md:text-base">
                    {{ $leagueData['round'] }}
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-around w-full">        
        @if ($teamsData['home'])
            <div class="flex flex-col items-center w-1/3 p-2">
                <img src="{{ asset($teamsData['home']['img']) }}"
                    class="w-12 h-12 md:w-16 md:h-16">

                <p class="p-2 text-sm font-black text-center text-gray-300 md:text-xl">{{ $teamsData['home']['name'] }}</p>
            </div>
        @endif

        <div class="w-1/3 pt-2">
            <div class="flex items-start justify-center">
                <p class="p-2 text-xl font-black text-gray-300 md:text-3xl">{{ $scoreData['fulltime']['home'] }}</p>
                <p class="p-2 text-xl font-black text-gray-300 md:text-3xl">-</p>
                <p class="p-2 text-xl font-black text-gray-300 md:text-3xl">{{ $scoreData['fulltime']['away'] }}</p>
            </div>
            
            <p class="p-2 text-base font-black text-center text-gray-300 md:text-xl">Finished</p>
        </div>

        @if ($teamsData['away'])
            <div class="flex flex-col items-center w-1/3 p-2">
                @if ($teamsData['away']['img'])
                    <img src="{{ asset($teamsData['away']['img']) }}"
                        class="w-12 h-12 md:w-16 md:h-16">
                @endif

                @unless($teamsData['away']['img'])
                    <div class="w-12 h-12 bg-gray-400 md:w-16 md:h-16">
                    </div>
                @endunless

                <p class="p-2 text-sm font-black text-center text-gray-300 md:text-xl">{{ $teamsData['away']['name'] }}</p>
            </div>
        @endif
    </div>
</div>