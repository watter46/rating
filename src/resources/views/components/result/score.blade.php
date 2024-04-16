<div {{ $attributes->merge(['class' => 'w-full p-1']) }}>
    <div class="flex items-center justify-center w-full">     
        <div class="grid w-full grid-cols-12">
            <div class="flex items-center justify-center h-full col-span-6 col-start-4 sm:col-span-4 sm:col-start-5">
                <p class="text-2xl font-black text-gray-300 md:text-3xl">Your Rating</p>
            </div>

            <div class="flex self-end justify-center h-full col-span-3 sm:col-span-4 gap-x-2">
                <div class="grid items-center w-fit">
                    @if ($teamsData['home'])
                        @if ($teamsData['home']['img'])
                            <img src="{{ $teamsData['home']['img'] }}"
                                class="w-7 h-7 md:w-10 md:h-10">
                        @endif
    
                        @unless($teamsData['home']['img'])
                            <div class="bg-gray-400 w-7 h-7 md:w-10 md:h-10">
                            </div>
                        @endunless
                    @endif
                </div>

                <div class="items-center hidden sm:grid">
                    <div class="flex items-start justify-center">
                        <p class="p-1 text-xl font-black text-gray-300 md:text-3xl">{{ $scoreData['fulltime']['home'] }}</p>
                        <p class="p-1 text-xl font-black text-gray-300 md:text-3xl">-</p>
                        <p class="p-1 text-xl font-black text-gray-300 md:text-3xl">{{ $scoreData['fulltime']['away'] }}</p>
                    </div>
                </div>

                <div class="grid items-center w-fit">
                    @if ($teamsData['away'])
                        @if ($teamsData['away']['img'])
                            <img src="{{ $teamsData['away']['img'] }}"
                                class="w-7 h-7 md:w-10 md:h-10">
                        @endif
    
                        @unless($teamsData['away']['img'])
                            <div class="bg-gray-400 w-7 h-7 md:w-10 md:h-10">
                            </div>
                        @endunless
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>