<div class="w-full rounded-2xl bg-sky-950 {{ $fixture->dataExists ? 'border-2 border-orange-600' : '' }}"
    x-data="{ isOpen: false }"
    x-cloak
    @close-fixture-modal.window="isOpen = false">
    <template x-if="isOpen">
        <div class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full"
            style="background: rgba(0, 0, 0, 0.8); overflow:hidden;">            
            <div class="flex justify-center w-4/6 p-5 rounded-lg bg-sky-950 h-3/6">
                <div class="flex flex-col w-1/2 h-full p-3">
                    <div class="flex items-center justify-between px-3 bg-gray-500 rounded-lg"
                        title="{{ $fixture->score['league']['round'] }}">
                        <div class="flex items-center p-2">
                            <img src="{{ $fixture->score['league']['img'] }}"
                                class="cursor-pointer w-14 h-14">

                            <div class="flex justify-center p-2">
                                <p class="text-xl font-black"
                                    style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                                    {{ $fixture->score['league']['name'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center w-1/4 h-full px-2 py-1 font-black text-gray-300">
                            {{ $fixture->score['fixture']['date'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-around w-full h-full">
                        @if ($fixture->score['teams']['home'])
                            <div class="flex flex-col items-center w-1/2 pl-2">
                                @if ($fixture->score['teams']['home']['img'])
                                    <img src="{{ $fixture->score['teams']['home']['img'] }}"
                                        class="w-24 h-24 cursor-pointer">
                                @endif

                                @unless($fixture->score['teams']['home']['img'])
                                    <div class="w-24 h-24 bg-gray-400 cursor-pointer">
                                    </div>
                                @endunless

                                <p class="p-2 text-2xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $fixture->score['teams']['home']['name'] }}
                                </p>
                            </div>
                        @endif

                        @if ($fixture->score['teams']['away'])
                            <div class="flex flex-col items-center w-1/2 pl-2">
                                @if ($fixture->score['teams']['away']['img'])
                                    <img src="{{ $fixture->score['teams']['away']['img'] }}"
                                        class="w-24 h-24 cursor-pointer">
                                @endif

                                @unless($fixture->score['teams']['away']['img'])
                                    <div class="w-24 h-24 bg-gray-400 cursor-pointer">
                                    </div>
                                @endunless

                                <p class="p-2 text-2xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $fixture->score['teams']['away']['name'] }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="relative flex justify-center w-1/2">
                    <div class="flex flex-col justify-center w-2/3 gap-y-3">
                        <p class="text-4xl font-black text-center text-gray-200">Refresh Key</p>

                        <input type="password" class="rounded-lg" wire:model="refreshKey">
                    </div>
                    
                    <div class="absolute bottom-0 flex justify-end w-full p-3 gap-x-5">
                        <button type="button" @click="isOpen = false" class="px-5 py-1 bg-gray-600 border-2 border-gray-200 rounded-lg">
                            <p class="font-black text-white">Cancel</p>
                        </button>

                        <x-admin.register-button wire:click="refresh">
                            Update
                        </x-admin.register-button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <template x-if="!isOpen">        
        <div class="flex flex-col items-center w-full h-full cursor-pointer"
            @click="isOpen = true">

            <div class="w-full p-1 overflow-hidden border-b border-gray-500">
                <div class="w-full h-full cursor-pointer">
            
                    <div class="flex justify-between w-full px-5">
                        <p class="text-gray-400">{{ $fixture->score['fixture']['date'] }}</p>
            
                        <div class="flex justify-end gap-x-2">
                            <p class="text-sm font-black text-gray-300">{{ $fixture->score['league']['name'] }}</p>
                            <p class="text-sm font-black text-gray-300">{{ $fixture->score['league']['round'] }}</p>
                            <img src="{{ $fixture->score['league']['img'] }}" class="w-5 h-5 bg-pink-500 rounded-xl">
                        </div>
                    </div>
                  
                    <div class="flex items-center justify-center w-full">        
                        @if ($fixture->score['teams']['home'])
                            <div class="flex items-center justify-end w-full px-10">
                                <div class="flex items-center justify-end w-2/3 h-full">
                                    <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                                        {{ $fixture->score['teams']['home']['name'] }}
                                    </p>
            
                                    @if ($fixture->score['teams']['home']['img'])
                                        <img src="{{ $fixture->score['teams']['home']['img'] }}"
                                            class="w-8 h-8">
                                    @endif
            
                                    @unless($fixture->score['teams']['home']['img'])
                                        <div class="w-8 h-8 bg-gray-400">
                                        </div>
                                    @endunless
                                </div>
                            </div>
                        @endif

                        <div class="w-44">
                            @if($fixture->fixture)
                                <div class="flex justify-center text-2xl font-black text-gray-300 rounded-xl
                                    {{ $fixture->winner ? 'bg-green-500' : (
                                    $fixture->winner === false
                                            ? 'bg-red-600'
                                            : 'bg-gray-500'
                                    ) }}">
                                    <div class="flex px-2 rounded-lg w-fit">
                                        <p class="px-1">{{ $fixture->fixture['score']['fulltime']['home'] }}</p>
                                        <p class="px-1">-</p>
                                        <p class="px-1">{{ $fixture->fixture['score']['fulltime']['away'] }}</p>
                                    </div>
                                </div>
                            @endif

                            @unless($fixture->fixture)
                                <div class="text-2xl font-black text-center text-gray-300">vs</div>
                            @endunless
                        </div>
                        
                        @if ($fixture->score['teams']['away'])
                            <div class="flex items-center w-full px-10">
                                @if ($fixture->score['teams']['away']['img'])
                                    <img src="{{ $fixture->score['teams']['away']['img'] }}"
                                        class="w-8 h-8">
                                @endif
            
                                @unless($fixture->score['teams']['away']['img'])
                                    <div class="w-8 h-8 bg-gray-400">
                                    </div>
                                @endunless
            
                                <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $fixture->score['teams']['away']['name'] }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>