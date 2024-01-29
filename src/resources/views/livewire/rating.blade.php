<div x-data="{
        rating: {{ $rating }},
        mom: @entangle('mom'),
        canEvaluate: @entangle('canEvaluate')
    }">
    <div class="w-full border-t-2 border-gray-700"></div>

    <div class="w-full px-10 py-2">
        <div class="flex flex-col w-full h-full">
            <p class="mb-3 text-2xl font-bold text-center text-gray-100 whitespace-nowrap">
                Your Rating
            </p>

            <div :class="!canEvaluate ? 'pointer-events-none opacity-30' : ''">
                <input id="ratingRange" class="w-full" type="range" min="0.1" max="10" step="0.1" x-model="rating">
                
                <div class="flex justify-center mt-3">
                    <div class="flex items-center justify-center w-1/3 border-2 border-gray-200 rounded-lg"
                        :style="`background-color: ${ratingBgColor(rating)}`">
                        <p class="py-1 text-2xl font-black text-gray-200" x-text="ratingValue(rating)"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-8 gap-x-5">
        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg bg-amber-400"
            :class="mom || !canEvaluate ? 'pointer-events-none opacity-30' : ''"
            wire:click="decideMOM">
            <p class="font-bold text-gray-200">★ MOM</p>
        </button>
        
        <button class="px-8 py-1 border-2 border-gray-200 rounded-lg pointer-events-none opacity-30 bg-sky-600"
            :class="!canEvaluate ? 'pointer-events-none opacity-30' : ''"
            x-init="$watch('rating', () => {
                if (!canEvaluate) return;

                $el.classList.remove('pointer-events-none', 'opacity-30');
            })"
            wire:click="evaluate(rating)">
            <p class="font-bold text-gray-200">Evaluate</p>
        </button>
    </div>

    @vite('resources/js/rating.js')
</div>