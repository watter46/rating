<div x-data="{
        rating: @entangle('rating'),
        ratingInput: null,
        mom: @entangle('mom'),
        canRate: @entangle('canRate')
    }"
    x-init="ratingInput = rating, $watch('rating', (rating) => ratingInput = rating)"
    class="w-full">
    
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
            <div class="w-full rounded-lg bg-gray-800 grid-flow-col grid gap-1 grid-cols-{{ $momLimit }}">
                @foreach($remainingMomCountRange as $count)
                    <x-svg.remaining-count-image class="fill-amber-300" />
                @endforeach

                @foreach($momCountRange as $count)
                    <x-svg.count-image />
                @endforeach
            </div>

            <button class="px-8 py-1 border-2 border-gray-200 rounded-lg bg-amber-400"
                :class="mom || !canRate ? 'pointer-events-none opacity-30' : ''"
                wire:click="decideMOM">
                <p class="font-bold text-gray-200">★ MOM</p>
            </button>
        </div>

        <div class="w-fit">
            <div class="w-full bg-gray-800 rounded-lg grid-flow-col grid gap-1 grid-cols-{{ $rateLimit }}">
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

    @vite('resources/js/rating.js')
</div>