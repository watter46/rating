<div x-data="{
        rating: @entangle('rating')
    }">
    <div class="w-full h-full mt-2 border-t-2 border-gray-700"></div>

    <div class="w-full px-10 py-5">
        <div class="flex flex-col w-full h-full">
            <p class="mb-3 text-3xl font-bold text-center text-gray-100 whitespace-nowrap">
                Rating
            </p>

            <input id="ratingRange" class="w-full" type="range" min="0" max="10" step="0.1" x-model="rating">
            
            <div class="flex justify-center mt-5">
                <div class="flex items-center justify-center w-1/3 border-2 border-gray-200 rounded-lg"
                    :style="`background-color: ${ratingBgColor(rating)}`">
                    <p class="py-1.5 text-3xl font-black text-gray-200" x-text="paddingZero(rating)"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-3">
        <button class="px-10 py-3 border-2 border-gray-200 rounded-lg bg-sky-600"
            wire:click="evaluate('{{ $fixtureId }}', '{{ $playerId }}', rating)">
            <p class="text-xl font-bold text-gray-200">Evaluate</p>
        </button>
    </div>

    @vite('resources/js/rating.js')
</div>