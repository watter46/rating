<div {{ $attributes->merge(['class' => 'w-full p-1']) }}>
    <div class="flex items-center justify-center w-full gap-x-3">        
        @if ($teams['home'])
            <div class="flex items-center justify-end w-5/12 h-full">
                <p class="p-2 text-sm font-black text-center text-gray-300 md:text-2xl">{{ $teams['home']['name'] }}</p>
                
                @if ($teams['home']['img'])
                    <img src="{{ $teams['home']['img'] }}"
                        class="w-8 h-8 md:w-14 md:h-14">
                @endif

                @unless($teams['home']['img'])
                    <div class="w-8 h-8 bg-gray-400 md:w-14 md:h-14">
                    </div>
                @endunless
            </div>
        @endif

        <div class="w-1/12">
            <div class="flex items-start justify-center md:px-3">
                <p class="p-1 text-xl font-black text-gray-300 md:text-3xl">{{ $score['fulltime']['home'] }}</p>
                <p class="p-1 text-xl font-black text-gray-300 md:text-3xl">-</p>
                <p class="p-1 text-xl font-black text-gray-300 md:text-3xl">{{ $score['fulltime']['away'] }}</p>
            </div>
        </div>

        @if ($teams['away'])
            <div class="flex items-center justify-start w-5/12 h-full">
                @if ($teams['away']['img'])
                    <img src="{{ $teams['away']['img'] }}"
                        class="w-8 h-8 md:w-14 md:h-14">
                @endif

                @unless($teams['away']['img'])
                    <div class="w-8 h-8 bg-gray-400 md:w-14 md:h-14">
                    </div>
                @endunless

                <p class="p-2 text-sm font-black text-center text-gray-300 md:text-2xl">{{ $teams['away']['name'] }}</p>
            </div>
        @endif
    </div>
</div>