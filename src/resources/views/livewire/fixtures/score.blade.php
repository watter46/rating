<div class="w-full p-1 overflow-hidden border-b border-gray-500">
    <div class="w-full h-full cursor-pointer"
        wire:click="toFixture">
        
        <div class="flex justify-between w-full mb-4 font-black">
            <div class="flex">
                <!-- Date -->
                <p class="pr-2 text-xs text-gray-400 md:text-lg">{{ $score['fixture']['date'] }}</p>

                <!-- IsRate -->
                @if ($isRate)
                    <div class="flex items-center justify-center h-full w-fit"
                        title="Rated">
                        <x-svg.rated-icon-image
                            class="w-3 h-3 md:w-4 md:h-4" />
                    </div>
                @endif
            </div>

            <!-- League -->
            <div class="flex justify-end gap-x-2">
                <p class="text-xs text-gray-300 truncate md:text-base">{{ $score['league']['name'] }}</p>
                <p class="text-xs text-gray-300 truncate md:text-base">{{ $score['league']['round'] }}</p>
                <img src="{{ $score['league']['img'] }}" class="hidden w-5 h-5 bg-pink-500 rounded-xl md:block">
            </div>
        </div>
      
        <div class="relative flex items-center justify-center w-full md:gap-x-3">        
            <!-- HomeTeam -->
            @if ($score['teams']['home'])
                <div class="flex items-center justify-end w-full">
                    <div class="flex items-center justify-end h-full mr-1 space-x-1">
                        <p class="text-sm font-black text-gray-300 truncate md:text-xl whitespace-nowrap">
                            {{ $score['teams']['home']['name'] }}
                        </p>

                        @if ($score['teams']['home']['img'])
                            <img src="{{ $score['teams']['home']['img'] }}"
                                class="w-6 h-6 md:w-10 md:h-10">
                        @endif

                        @unless($score['teams']['home']['img'])
                            <div class="w-6 h-6 bg-gray-400 md:w-10 md:h-10"></div>
                        @endunless
                    </div>
                </div>
            @endif
    
            <!-- Score -->
            <div class="flex justify-center font-black text-gray-300 rounded
                {{ $winner
                    ? 'bg-green-500'
                    : ($winner === false ? 'bg-red-600' : 'bg-gray-500') }}">
                <div class="flex px-1 space-x-1.5 md:px-3 w-fit md:text-xl">
                    <p>{{ $fixture['score']['fulltime']['home'] }}</p>
                    <p>-</p>
                    <p>{{ $fixture['score']['fulltime']['away'] }}</p>
                </div>
            </div>
    
            <!-- AwayTeam -->
            @if ($score['teams']['away'])
                <div class="flex items-center justify-start w-full">
                    <div class="flex items-center justify-end h-full ml-1 space-x-1">                        
                        @if ($score['teams']['away']['img'])
                            <img src="{{ $score['teams']['away']['img'] }}"
                                class="w-6 h-6 md:w-10 md:h-10">
                        @endif

                        @unless($score['teams']['away']['img'])
                            <div class="w-6 h-6 bg-gray-400 md:w-10 md:h-10"></div>
                        @endunless

                        <p class="text-sm font-black text-gray-300 truncate md:text-xl whitespace-nowrap">
                            {{ $score['teams']['away']['name'] }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>