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
        },
        isMobile: false,
    }"
    x-init="() => {
        if(window.innerWidth <= 768) { 
            isMobile = true; 
        } 
        window.addEventListener('resize', () => { 
            isMobile = window.innerWidth <= 768; 
        });
    }">
    
    @foreach($players as $index => $player)
        <div class="flex flex-col w-full p-2 font-black text-gray-300 border-b border-gray-500 gap-x-5">
            <div class="flex w-full">
                <div class="flex items-center justify-center w-1/6">{{ $player['position'] }}</div>
                
                <div class="flex justify-center w-1/6">
                    <img src="{{ $player['img']['data'] }}" class="rounded-full w-14 h-14">
                </div>
                
                <div class="flex flex-auto pl-5">
                    <div class="flex items-center h-full gap-x-2">
                        <p class="text-xl">{{ $player['number'] }}</p>
                        <p class="text-xl">{{ $this->toLastName($player['name']) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center w-full p-1">
                <div class="px-2">
                    <button x-show="!isMobile" class="px-4 py-1 border-2 border-gray-200 rounded-lg h-fit bg-amber-400"
                        :class="players[{{ $index }}].mom ? '' : 'opacity-30'"
                        @click="selectMom({{ $index }}, players[{{ $index }}].mom)">
                        <p class="font-bold text-center text-gray-200">★MOM</p>
                    </button>
                    
                    <button x-show="isMobile"
                        class="w-full text-center"
                        :class="players[{{ $index }}].mom ? '' : 'opacity-30'"
                        @click="selectMom({{ $index }}, players[{{ $index }}].mom)">
                        <p class="text-xs font-bold text-center text-gray-400">MOM</p>
                        <p class="text-2xl font-bold text-center text-amber-400">★</p>
                    </button>
                </div>
                
                <div class="grid content-center w-full h-full">
                    <input id="ratingRange"
                        class="px-2"
                        type="range" min="0.1" max="10" step="0.1"
                        x-model="players[{{ $index }}].rating">
                </div>
                
                <div class="grid content-center w-16">
                    <div class="flex items-center justify-center border-2 border-gray-200 rounded-lg pointer-events-none opacity-30"
                        :style="`background-color: ${ratingBgColor(players[{{ $index }}].rating)}`"
                        x-init="$watch(`players[{{ $index }}].rating`, () => {            
                            $el.classList.remove('pointer-events-none', 'opacity-30');
                        })">
                        <p class="text-lg font-black text-gray-200"
                            x-text="ratingValue(players[{{ $index }}].rating)">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="flex justify-end w-full p-5">
        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg pointer-events-none opacity-30 bg-sky-600"
            :class="!canRated ? 'pointer-events-none opacity-30' : ''"
            x-init="$watch('players', () => {
                $el.classList.remove('pointer-events-none', 'opacity-30');
            })"
            wire:click="rateAll(result())">
            <p class="text-xl font-black text-gray-300">Rate</p>
        </button>
    </div>
</div>