<div class="flex flex-col w-full"
    x-data="{
        players: @js($players),
        canRated: @entangle('canRated'),
        selectMom(index, mom) {
            this.players.forEach((p, i) => {
                if (i === index) {
                    this.players[i].mom = !this.players[i].mom;
                    return;
                }

                this.players[i].mom = false;
            })
        },
        result() {
            return this.players.map(player => {
                return {
                    id: player.id,
                    mom: player.mom,
                    rating: player.rating
                };
            });
        }
    }">
    
    @foreach($players as $index => $player)
        <div class="flex flex-col w-full p-2 border-b border-gray-500 md:gap-y-2 md:p-4">
            <!-- Profile -->
            <div class="flex w-full">
                <div class="flex justify-center w-fit">
                    <img src="{{ $player['img']['data'] }}" class="rounded-full w-14 h-14 md:w-20 md:h-20">
                </div>
                
                <div class="flex items-center flex-auto pl-3 space-x-2">
                    <p class="text-lg font-black text-gray-300 md:text-2xl">
                        {{ $player['position'] }}
                    </p>
                    <p class="text-lg font-black text-gray-300 md:text-2xl">
                        {{ $player['number'] }}
                    </p>
                    
                    <p class="text-xl font-black text-gray-300 md:text-3xl">
                        {{ $this->toLastName($player['name']) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-center w-full p-1 md:p-2">
                <!-- MOM -->
                <div class="px-2 md:px-3">
                    <button
                        class="w-full text-center"
                        :class="players[{{ $index }}].mom ? '' : 'opacity-30'"
                        @click="selectMom({{ $index }}, players[{{ $index }}].mom)">
                        <p class="text-xs font-bold text-center text-gray-400 md:text-lg">MOM</p>
                        <p class="text-2xl font-bold text-center text-amber-400 md:text-3xl">★</p>
                    </button>
                </div>
                
                <!-- RatingSlider -->
                <div class="grid content-center w-full h-full">
                    <input id="ratingRange"
                        class="px-2"
                        type="range" min="0.1" max="10" step="0.1"
                        x-model="players[{{ $index }}].rating">
                </div>
                
                <!-- Rating -->
                <div class="grid content-center w-16 md:w-28">
                    <div class="flex items-center justify-center border-2 border-gray-200 rounded-lg pointer-events-none opacity-30 md:py-1.5"
                        :style="`background-color: ${ratingBgColor(players[{{ $index }}].rating)}`"
                        x-init="$watch(`players[{{ $index }}].rating`, () => {            
                            $el.classList.remove('pointer-events-none', 'opacity-30');
                        })">
                        <p class="text-lg font-black text-gray-200 md:text-2xl"
                            x-text="ratingValue(players[{{ $index }}].rating)">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- RateButton -->
    <div class="flex justify-end w-full p-5">
        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg pointer-events-none md:px-12 opacity-30 bg-sky-600"
            :class="!canRated ? 'pointer-events-none opacity-30' : ''"
            x-init="$watch('players', () => {
                $el.classList.remove('pointer-events-none', 'opacity-30');
            })"
            wire:click="rateAll(result())">
            <p class="text-xl font-black text-gray-300 md:text-2xl">Rate</p>
        </button>
    </div>
</div>