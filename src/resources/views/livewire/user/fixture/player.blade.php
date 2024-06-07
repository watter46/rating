<x-util.modal-button class="w-full md:w-8/12">
    <x-slot:icon>
        <div id="{{ $name }}" class="h-full {{ $size }}"
            :class=" componentName === 'startXI' ? 'invisible' : ''"
            x-data="{ componentName: @entangle('name') }"
            wire:ignore.self>
            
            <div class="flex justify-center player">
                <div class="relative flex justify-center w-fit">
                    <!-- PlayerImage -->
                    <x-player.player-image
                        class="{{ $size }} cursor-default"
                        :number="$player['number']"
                        :img="$player['img']" />

                    <!-- Goals -->
                    <div class="absolute top-[-10%] right-[77%]">
                        <x-player.goals
                            class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                            :goals="$player['goals']" />
                    </div>

                    <!-- Assists -->
                    <div class="absolute top-[-10%] left-[77%]">
                        <x-player.assists
                            class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                            :assists="$player['assists']" />
                    </div>

                    <div class="absolute bottom-[-10%] left-[65%] min-w-[40px]"
                        x-data="{
                            toggleStates: 'my',
                            isMy() { return this.toggleStates === 'my' },
                            isUsers() { return this.toggleStates === 'users' },
                            isMachine() { return this.toggleStates === 'machine' },
                            
                            myMom: @entangle('player.ratings.my.mom'),
                            myRating: @entangle('player.ratings.my.rating'),
                            machineRating: @entangle('player.ratings.machine'),
                            usersRating: @entangle('player.ratings.users.rating'),
                            usersMom: @entangle('player.ratings.users.mom'),
                        }"
                        @toggle-states-updated.window="toggleStates = event.detail.state">
                        
                        <!-- MyRating -->
                        <template x-if="isMy()">
                            <div class="flex items-center justify-center px-1 rounded-xl"
                                :style=" myMom
                                    ? 'background-color: #0E87E0'
                                    : `background-color: ${ratingBgColor(myRating)}`
                                ">

                                <template x-if="myMom">
                                    <p class="text-xs font-black text-gray-50">★</p>
                                </template>
                                
                                <p class="text-xs font-black md:text-sm text-gray-50"
                                    x-text="ratingValue(myRating)">
                                </p>
                            </div>
                        </template>

                        <!-- UserRating -->
                        <template x-if="isUsers()">
                            <div class="flex items-center justify-center px-1 rounded-xl"
                                :style=" usersMom
                                    ? 'background-color: #0E87E0'
                                    : `background-color: ${ratingBgColor(usersRating)}`
                                ">

                                <template x-if="usersMom">
                                    <p class="text-xs font-black text-gray-50">★</p>
                                </template>
                                
                                <p class="text-xs font-black md:text-sm text-gray-50"
                                    x-text="ratingValue(usersRating)">
                                </p>
                            </div>
                        </template>

                        <!-- MachineRating -->
                        <template x-if="isMachine()">
                            <div class="flex items-center justify-center rounded-xl"
                                :style="`background-color: ${ratingBgColor(machineRating)}`">
                                
                                <p class="text-xs font-black md:text-sm text-gray-50"
                                    x-text="ratingValue(machineRating)">
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

    <div class="flex flex-col items-stretch w-full p-3">
        <!-- PlayerDetail -->
        <x-fixture.player-detail :$player />
    
        <!-- Rating -->
        <div class="flex items-center justify-center w-full h-full border-t-2 border-gray-700">
            <div x-data="{
                    ratingInput: null,
                    myRating: @entangle('player.ratings.my.rating'),
                    myMom: @entangle('player.ratings.my.mom'),
                    canRate: @entangle('player.canRate'),
                    canMom: @entangle('player.canMom'),
                }"
                x-init="ratingInput = myRating, $watch('myRating', (myRating) => ratingInput = myRating)"
                class="w-full"
                @mom-button-disabled.window="canMom = false">
            
                <div class="px-10 py-2">
                    <div class="flex flex-col h-full">
                        <p class="mb-3 text-2xl font-bold text-center text-gray-100 whitespace-nowrap">
                            Your Rating
                        </p>
            
                        <div :class="!canRate ? 'pointer-events-none opacity-30' : ''">
                            <input id="ratingRange" type="range" min="0.1" max="10" step="0.1" x-model="ratingInput">
                            
                            <div class="flex justify-center mt-3">
                                <div class="flex items-center justify-center w-1/2 border-2 border-gray-200 rounded-lg"
                                    :style="`background-color: ${ratingBgColor(ratingInput)}`">
                                    <p class="py-1 text-xl font-black text-gray-200" x-text="ratingValue(ratingInput)"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="flex justify-end mt-8 gap-x-5">
                    <div class="w-fit">
                        <div class="w-full mb-1 rounded-lg bg-gray-800 grid-flow-col grid gap-1
                            grid-cols-{{ $player['momLimit'] }}">
                            @foreach($remainingMomCountRange as $count)
                                <x-svg.remaining-count-image class="fill-amber-300" />
                            @endforeach
            
                            @foreach($momCountRange as $count)
                                <x-svg.count-image />
                            @endforeach
                        </div>
            
                        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg bg-amber-400"
                            :class="!canMom ? 'pointer-events-none opacity-30' : ''"
                            wire:click="decideMom">
                            <p class="font-bold text-gray-200">★ MOM</p>
                        </button>
                    </div>
            
                    <div class="w-fit">
                        <div class="w-full mb-1 bg-gray-800 rounded-lg grid-flow-col grid gap-1
                            grid-cols-{{ $player['rateLimit'] }}">
                            @foreach($remainingRateCountRange as $count)
                                <x-svg.remaining-count-image class="fill-sky-500" />
                            @endforeach
            
                            @foreach($rateCountRange as $count)
                                <x-svg.count-image />
                            @endforeach
                        </div>
            
                        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg pointer-events-none opacity-30 bg-sky-600"
                            :class="!canRate ? 'pointer-events-none opacity-30' : ''"
                            x-init="$watch('ratingInput', () => {
                                if (!canRate) return;
            
                                $el.classList.remove('pointer-events-none', 'opacity-30');
                            })"
                            wire:click="rate(ratingInput)">
                            <p class="font-bold text-gray-200">Rate</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/rating.js', 'resources/css/rating.css'])
</x-util.modal-button>