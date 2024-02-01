<div class="flex flex-col w-full"
    x-data="{
        players: @js($players),
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
        <div class="flex w-full p-3 font-black text-gray-300 border-b border-gray-500 gap-x-5">
            <div class="w-20 text-xl text-center">{{ $player['position'] }}</div>

            <img src="{{ $player['img']['data'] }}" class="w-16 h-16 rounded-full">

            <div class="flex items-center w-1/3 h-full gap-x-2">
                <p class="text-lg">{{ $player['number'] }}</p>
                <p class="text-xl">{{ $this->toLastName($player['name']) }}</p>
            </div>

            <div class="flex items-center justify-center w-full">
                <button class="px-4 py-1 border-2 border-gray-200 rounded-lg h-fit bg-amber-400"
                    :class="players[{{ $index }}].mom ? '' : 'opacity-30'"
                    @click="selectMom({{ $index }}, players[{{ $index }}].mom)">
                    <p class="font-bold text-center text-gray-200">★MOM</p>
                </button>
                
                <div class="grid content-center w-4/6 h-full">
                    <input id="ratingRange"
                        class="scale-75" 
                        type="range" min="0.1" max="10" step="0.1"
                        x-model="players[{{ $index }}].rating">
                </div>
                
                <div class="grid content-center w-16">
                    <div class="flex items-center justify-center border-2 border-gray-200 rounded-lg"
                        :style="`background-color: ${ratingBgColor(players[{{ $index }}].rating)}`">
                        <p class="text-xl font-black text-gray-200"
                            x-text="ratingValue(players[{{ $index }}].rating)">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="flex justify-end w-full p-5">
        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg bg-sky-600"
            wire:click="rateAll(result())">
            <p class="text-xl font-black text-gray-300">Rate</p>
        </button>
    </div>
</div>