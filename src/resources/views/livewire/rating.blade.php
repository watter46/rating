<div x-data="{
        rating: @entangle('rating').live,
        mom: @entangle('mom').live
    }">
    <div class="w-full h-full border-t-2 border-gray-700"></div>

    <div class="w-full px-10 py-2">
        <div class="flex flex-col w-full h-full">
            <p class="mb-3 text-2xl font-bold text-center text-gray-100 whitespace-nowrap">
                Your Rating
            </p>

            <input id="ratingRange" class="w-full" type="range" min="0.1" max="10" step="0.1" x-model="rating">
            
            <div class="flex justify-center mt-3">
                <div class="flex items-center justify-center w-1/3 border-2 border-gray-200 rounded-lg"
                    :style="`background-color: ${ratingBgColor(rating)}`">
                    <p class="py-1.5 text-2xl font-black text-gray-200" x-text="ratingValue(rating)"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-2 gap-x-5">
        <button class="px-10 py-2 border-2 border-gray-200 rounded-lg bg-amber-400"
            :class="mom ? 'pointer-events-none opacity-30' : ''"
            wire:click="decideMOM">
            <p class="text-lg font-bold text-gray-200">☆ MOM</p>
        </button>
        
        <button class="px-10 py-2 border-2 border-gray-200 rounded-lg pointer-events-none opacity-30 bg-sky-600"
            x-init="$watch('rating', () => $el.classList.remove('pointer-events-none', 'opacity-30'))"
            wire:click="evaluate(rating)">
            <p class="text-lg font-bold text-gray-200">Evaluate</p>
        </button>
    </div>

    @vite('resources/js/rating.js')
</div>