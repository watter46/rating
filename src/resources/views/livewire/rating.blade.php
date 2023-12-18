<div x-data="{
        rating: {{ $rating }},
        ratingPaddingZero () {                                
            if (Number.isInteger(Number(this.rating))) {
                return `${this.rating}.0`;
            }

            return this.rating;
        },
        changeTipsBgColor () {
            if (this.rating < 6.0)                       return '#EB1C23';
            if (6.0 <= this.rating && this.rating < 6.5) return '#FF7B00';
            if (6.5 <= this.rating && this.rating < 7.0) return '#F4BB00';
            if (7.0 <= this.rating && this.rating < 8)   return '#C0CC00';
            if (8.0 <= this.rating && this.rating < 9.0) return '#5CB400';
            if (9.0 <= this.rating)                      return '#009E9E';
        }
    }">
    <div class="flex">
        <div class="flex flex-col w-full h-full mt-2 border-t-2 border-gray-700">
            <p class="text-2xl font-bold text-center text-gray-100 whitespace-nowrap">Machine Rating</p>
            <p class="text-3xl font-bold text-center text-gray-100 whitespace-nowrap">{{ $rating }}</p>
        </div>
    </div>

    <div class="w-full px-10 py-5">
        <div class="flex flex-col w-full h-full">
            <p class="mb-3 text-2xl font-bold text-center text-gray-100 whitespace-nowrap">
                Your Rating
            </p>

            <input id="ratingRange" class="w-full" type="range" min="0" max="10" step="0.1" x-model="rating">
            
            <div class="flex justify-center mt-5">
                <div class="flex items-center justify-center w-1/3 border-2 border-gray-200 rounded-lg"
                    :style="`background-color: ${changeTipsBgColor()}`">
                    <p class="py-1.5 text-3xl font-black text-gray-200" x-text="ratingPaddingZero()"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-3">
        <button class="px-10 py-3 border-2 border-gray-200 rounded-lg bg-sky-600"
            @click="$wire.dispatch('player-evaluate', {
                playerId: {{ $playerId }},
                rating: rating
            })">
            <p class="text-xl font-bold text-gray-200">Evaluate</p>
        </button>
    </div>
</div>