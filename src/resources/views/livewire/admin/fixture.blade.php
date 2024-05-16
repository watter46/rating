<div class="w-full rounded-2xl bg-sky-950 {{ $fixtureInfo->lineupsExists ? 'border-2 border-orange-600' : '' }}"
    x-data="{ isOpen: false }"
    x-cloak
    @close-fixture-modal.window="isOpen = false">
    <template x-if="isOpen">
        <div class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full"
            style="background: rgba(0, 0, 0, 0.8); overflow:hidden;">            
            <div class="flex justify-center w-4/6 p-5 rounded-lg bg-sky-950 h-3/6">
                <div class="flex flex-col w-1/2 h-full p-3">
                    <div class="flex items-center justify-between px-3 bg-gray-500 rounded-lg"
                        title="{{ $fixtureInfo->league['round'] }}">
                        <div class="flex items-center p-2">
                            <img src="{{ asset($fixtureInfo->league['img']) }}"
                                class="w-10 h-10 cursor-pointer">

                            <div class="flex justify-center p-2">
                                <p class="text-xl font-black"
                                    style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                                    {{ $fixtureInfo->league['name'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center w-1/4 h-full px-2 py-1 font-black text-gray-300">
                            {{ $fixtureInfo->fixture['first_half_at'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-around w-full h-full">
                        @if ($fixtureInfo->teams['home'])
                            <div class="flex flex-col items-center w-1/2 pl-2">
                                
                                <img src="{{ asset($fixtureInfo->teams['home']['img']) }}"
                                        class="w-24 h-24 cursor-pointer">

                                <p class="p-2 text-2xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $fixtureInfo->teams['home']['name'] }}
                                </p>
                            </div>
                        @endif

                        @if ($fixtureInfo->teams['away'])
                            <div class="flex flex-col items-center w-1/2 pl-2">
                                <img src="{{ asset($fixtureInfo->teams['away']['img']) }}"
                                    class="w-24 h-24 cursor-pointer">

                                <p class="p-2 text-2xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $fixtureInfo->teams['away']['name'] }}
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
                        <p class="text-gray-400">{{ $fixtureInfo->fixture['first_half_at'] }}</p>
            
                        <div class="flex justify-end gap-x-2">
                            <p class="text-sm font-black text-gray-300">{{ $fixtureInfo->league['name'] }}</p>
                            <p class="text-sm font-black text-gray-300">{{ $fixtureInfo->league['round'] }}</p>
                            <img src="{{ asset($fixtureInfo->league['img']) }}" class="w-5 h-5 bg-pink-500 rounded-xl">
                        </div>
                    </div>
                  
                    <div class="flex items-center justify-center w-full">        
                        @if ($fixtureInfo->teams['home'])
                            <div class="flex items-center justify-end w-full px-10">
                                <div class="flex items-center justify-end w-2/3 h-full">
                                    <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                                        {{ $fixtureInfo->teams['home']['name'] }}
                                    </p>
            
                                    <img src="{{ asset($fixtureInfo->teams['home']['img']) }}"
                                        class="w-8 h-8">
                                </div>
                            </div>
                        @endif

                        <div class="w-44">
                            @if($fixtureInfo->fixture)
                                <div class="flex justify-center text-2xl font-black text-gray-300 rounded-xl
                                {{ $fixtureInfo->fixture['winner']
                                ? 'bg-green-500'
                                : ($fixtureInfo->fixture['winner'] === false ? 'bg-red-600' : 'bg-gray-500') }}">
                                    <div class="flex px-2 rounded-lg w-fit">
                                        <p class="px-1">{{ $fixtureInfo->score['fulltime']['home'] }}</p>
                                        <p class="px-1">-</p>
                                        <p class="px-1">{{ $fixtureInfo->score['fulltime']['away'] }}</p>
                                    </div>
                                </div>
                            @endif

                            @unless($fixtureInfo->fixture)
                                <div class="text-2xl font-black text-center text-gray-300">vs</div>
                            @endunless
                        </div>
                        
                        @if ($fixtureInfo->teams['away'])
                            <div class="flex items-center w-full px-10">
                                <img src="{{ asset($fixtureInfo->teams['away']['img']) }}"
                                    class="w-8 h-8">
            
                                <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $fixtureInfo->teams['away']['name'] }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>