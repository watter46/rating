<div class="flex items-center justify-center w-8 md:w-10 rounded-xl"
    x-data="{
        rating: @js($rating),
        mom: @js($mom)
    }"
    :style=" mom
        ? 'background-color: #0E87E0'
        : `background-color: ${ratingBgColor(rating)}`
    ">

    <template x-if="mom">
        <p class="text-xs font-black text-gray-50 md:text-base">★</p>
    </template>
    
    <p class="text-sm font-black text-gray-50 md:text-base"
        x-text="ratingValue(rating)">
    </p>

    @vite(['resources/js/rating.js'])
</div>