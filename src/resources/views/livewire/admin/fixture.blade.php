<div class="w-full rounded-2xl bg-sky-950 {{ $dataExists ? 'border-2 border-orange-600' : '' }}"
    x-data="{isOpen: false}"
    x-cloak
    @close-fixture-modal.window="isOpen = false">
    <template x-if="isOpen">
        <div class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full"
            style="background: rgba(0, 0, 0, 0.8); overflow:hidden;">            
            <div class="flex justify-center w-4/6 p-5 rounded-lg bg-sky-950 h-3/6">
                <div class="flex flex-col w-1/2 h-full p-3">
                    <div class="flex items-center justify-between px-3 bg-gray-500 rounded-lg"
                        title="{{ $score['league']['round'] }}">
                        <div class="flex items-center p-2">
                            <img src="{{ $score['league']['img'] }}"
                                class="cursor-pointer w-14 h-14">

                            <div class="flex justify-center p-2">
                                <p class="text-xl font-black"
                                    style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                                    {{ $score['league']['name'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center w-1/4 h-full px-2 py-1 font-black text-gray-300">
                            {{ $score['fixture']['date'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-around w-full h-full">
                        @if ($score['teams']['home'])
                            <div class="flex flex-col items-center w-1/2 pl-2">
                                @if ($score['teams']['home']['img'])
                                    <img src="{{ $score['teams']['home']['img'] }}"
                                        class="w-24 h-24 cursor-pointer">
                                @endif

                                @unless($score['teams']['home']['img'])
                                    <div class="w-24 h-24 bg-gray-400 cursor-pointer">
                                    </div>
                                @endunless

                                <p class="p-2 text-2xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $score['teams']['home']['name'] }}
                                </p>
                            </div>
                        @endif

                        @if ($score['teams']['away'])
                            <div class="flex flex-col items-center w-1/2 pl-2">
                                @if ($score['teams']['away']['img'])
                                    <img src="{{ $score['teams']['away']['img'] }}"
                                        class="w-24 h-24 cursor-pointer">
                                @endif

                                @unless($score['teams']['away']['img'])
                                    <div class="w-24 h-24 bg-gray-400 cursor-pointer">
                                    </div>
                                @endunless

                                <p class="p-2 text-2xl font-black text-gray-300 whitespace-nowrap">
                                    {{ $score['teams']['away']['name'] }}
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
    
                        <button type="submit" class="px-5 py-1 bg-green-600 border-2 border-gray-200 rounded-lg "
                            wire:click="refresh"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                            <p class="font-black text-white" wire:loading.class.add="hidden">Update</p>
                            <p class="hidden font-black text-white" wire:loading.class.remove="hidden">Saving...</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <template x-if="!isOpen">        
        <div class="flex items-center w-full h-full cursor-pointer"
            @click="isOpen = true">
            <div class="flex items-center justify-center w-1/4 h-full px-2 py-1 font-black text-gray-300 border-r border-gray-500">
                {{ $score['fixture']['date'] }}
            </div>

            <div class="flex flex-col justify-center w-2/4 px-2 py-1">
                @if ($score['teams']['home'])
                    <div class="flex items-center pl-2">
                        @if ($score['teams']['home']['img'])
                            <img src="{{ $score['teams']['home']['img'] }}"
                                class="w-8 h-8 cursor-pointer">
                        @endif

                        @unless($score['teams']['home']['img'])
                            <div class="w-8 h-8 bg-gray-400 cursor-pointer">
                            </div>
                        @endunless

                        <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                            {{ $score['teams']['home']['name'] }}
                        </p>
                    </div>
                @endif

                @if ($score['teams']['away'])
                    <div class="flex items-center pl-2">
                        @if ($score['teams']['away']['img'])
                            <img src="{{ $score['teams']['away']['img'] }}"
                                class="w-8 h-8 cursor-pointer">
                        @endif

                        @unless($score['teams']['away']['img'])
                            <div class="w-8 h-8 bg-gray-400 cursor-pointer">
                            </div>
                        @endunless

                        <p class="p-2 text-xl font-black text-gray-300 whitespace-nowrap">
                            {{ $score['teams']['away']['name'] }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-center w-1/4 h-full rounded-r-2xl bg-sky-900"
                title="{{ $score['league']['round'] }}">
                <div class="flex flex-col items-center p-2">
                    <img src="{{ $score['league']['img'] }}"
                            class="w-10 h-10 cursor-pointer">

                    <div class="flex justify-center p-2">
                        <p class="font-black text-center" style="text-shadow: #b8b8b8 2px 1px 5px; color: #37003C;">
                            {{ $score['league']['name'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>