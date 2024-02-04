<div class="w-full p-1 overflow-hidden border-b border-gray-500">
    <div class="w-full h-full cursor-pointer"
        wire:click="toFixture">

        <div class="flex justify-between w-full px-5">
            <p class="text-gray-400">{{ $score['fixture']['date'] }}</p>

            <div class="flex justify-end gap-x-2">
                <p class="text-sm font-black text-gray-300">{{ $score['league']['name'] }}</p>
                <p class="text-sm font-black text-gray-300">{{ $score['league']['round'] }}</p>
                <img src="{{ $score['league']['img'] }}" class="w-5 h-5 bg-pink-500 rounded-xl">
            </div>
        </div>
      
        <div class="relative flex items-center justify-center w-full">        
            @if ($isRate)
                <div class="absolute left-0 flex items-center justify-center h-full px-5 w-fit"
                    title="Rated">
                    <x-svg.rated-icon-image />
                </div>
            @endif

            @if ($score['teams']['home'])
                <div class="flex items-center justify-end w-full px-10">
                    <div class="flex items-center justify-end w-2/3 h-full">
                        <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                            {{ $score['teams']['home']['name'] }}
                        </p>

                        @if ($score['teams']['home']['img'])
                            <img src="{{ $score['teams']['home']['img'] }}"
                                class="w-8 h-8">
                        @endif

                        @unless($score['teams']['home']['img'])
                            <div class="w-8 h-8 bg-gray-400">
                            </div>
                        @endunless
                    </div>
                </div>
            @endif
    
            <div class="flex justify-center text-2xl font-black text-gray-300 rounded-xl
                {{ $winner ? 'bg-green-500' : (
                   $winner === false
                        ? 'bg-red-600'
                        : 'bg-gray-500'
                ) }}">
                <div class="flex px-2 rounded-lg w-fit">
                    <p class="px-1">{{ $fixture['score']['fulltime']['home'] }}</p>
                    <p class="px-1">-</p>
                    <p class="px-1">{{ $fixture['score']['fulltime']['away'] }}</p>
                </div>
            </div>
    
            @if ($score['teams']['away'])
                <div class="flex items-center w-full px-10">
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
    </div>
</div>