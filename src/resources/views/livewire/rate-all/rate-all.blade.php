<div class="w-full h-full md:px-10 lg:px-16"
    x-data="{
        players: @entangle('players'),
        profiles: @entangle('profiles')
    }">
    <div class="w-full h-full px-5">
        <template x-for="(profile, index) in profiles" :key="profile.id">
            <div class="flex flex-col w-full p-2 border-b border-gray-500 gap-y-2">
                <!-- Profile -->
                <div class="flex w-full">
                    <div class="flex justify-center w-fit">
                        <img :src="profile.img.data" class="rounded-full w-14 h-14">
                    </div>
                    
                    <div class="flex items-center flex-auto pl-3 space-x-2">
                        <p class="text-lg font-black text-gray-300 md:text-lg"
                            x-text="profile.position"></p>
                        
                        <p class="text-lg font-black text-gray-300 md:text-lg"
                            x-text="profile.number"></p>
                        
                        <p class="text-xl font-black text-gray-300 md:text-xl"
                            x-text="profile.name"></p>
                    </div>
                </div>

                <div class="flex items-center justify-center w-full gap-x-3">
                    <!-- MOM -->
                    <div class="px-2">
                        <button
                            class="w-full text-center"
                            :class="players[index].mom ? '' : 'opacity-30'"
                            @click="players = selectMom(players, index)">
                            <p class="text-xs font-bold text-center text-gray-400">MOM</p>
                            <p class="text-xl font-bold text-center text-amber-400">★</p>
                        </button>
                    </div>
                    
                    <!-- RatingSlider -->
                    <div class="grid content-center w-full">
                        <input id="ratingRange"
                            type="range" min="0.1" max="10" step="0.1"
                            x-model.number="players[index].rating"
                            @input="activeRating(index)">
                    </div>
                    
                    <!-- Rating -->
                    <div class="grid content-center w-16 md:w-20">
                        <div :id=`rating-${index}` class="flex items-center justify-center border-2 border-gray-200 rounded-lg pointer-events-none opacity-30"
                            :style="`background-color: ${ratingBgColor(players[index].rating)}`">
                            <p class="text-lg font-black text-gray-200 md:text-xl"
                                x-text="ratingValue(players[index].rating)">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- RateButton -->
        <div class="flex justify-end w-full p-5">
            <button class="px-8 py-1 border-2 border-gray-200 rounded-lg pointer-events-none opacity-30 md:px-8 bg-sky-600"
                x-init="$watch('players', (players) => activeEl($el))"
                wire:click="rateAll(getResultRatings(players))">
                <p class="text-lg font-black text-gray-300">Rate</p>
            </button>
        </div>
    </div>

    @vite('resources/js/rateAll.js')
</div>