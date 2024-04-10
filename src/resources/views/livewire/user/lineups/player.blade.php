<x-util.modal-button class="w-full md:w-8/12">
    <x-slot:icon>
        <div id="{{ $name }}" class="h-full {{ $size }}"
            :class=" componentName === 'startXI' ? 'invisible' : ''"
            x-data="{
                rating: @entangle('rating'),
                mom: @entangle('mom'),
                machine: @entangle('defaultRating'),
                componentName: @entangle('name')
            }"
            wire:ignore.self>
            
            <div class="flex justify-center player">
                <div class="relative flex justify-center w-fit">
                    <!-- PlayerImage -->
                    <x-player.player-image
                        class="{{ $size }} cursor-default"
                        :number="$player['number']"
                        :img="$player['img']" />

                    <!-- Goals -->
                    <div class="absolute top-0 left-0 -translate-x-[60%]">
                        <x-player.goals
                            class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                            :goals="$player['goal']" />
                    </div>

                    <!-- Assists -->
                    <div class="absolute top-0 right-0 translate-x-[60%]">
                        <x-player.assists
                            class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                            :assists="$player['assists']" />
                    </div>
                    
                    <!-- Rating -->
                    <div class="absolute bottom-0 right-0 translate-x-[45%]"
                        x-data="{ isUser: true }"
                        @user-machine-toggled.window="isUser = !isUser">
                        
                        <!-- UserRating -->
                        <template x-if="isUser">
                            <div class="flex items-center justify-center w-8 rounded-xl"
                                :style=" mom
                                    ? 'background-color: #0E87E0'
                                    : `background-color: ${ratingBgColor(rating)}`
                                ">

                                <template x-if="mom">
                                    <p class="text-xs font-black text-gray-50">★</p>
                                </template>
                                
                                <p class="text-xs font-black md:text-sm text-gray-50"
                                    x-text="ratingValue(rating)">
                                </p>
                            </div>
                        </template>

                        <!-- MachineRating -->
                        <template x-if="!isUser">
                            <div class="flex items-center justify-center w-8 rounded-xl"
                                :style="`background-color: ${ratingBgColor(machine)}`">
                                
                                <p class="text-xs font-black md:text-sm text-gray-50"
                                    x-text="ratingValue(machine)">
                                </p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center pointer-events-none gap-x-2">            
                <p class="text-xs font-black text-white md:text-sm">
                    {{ $player['name'] }}
                </p>
            </div>
        </div>

        @vite(['resources/css/player.css'])
    </x-slot:icon>

    <x-slot:disabled-icon></x-slot:disabled-icon>

    <x-fixture.player-detail
        :$player 
        :$fixtureId />
</x-util.modal-button>