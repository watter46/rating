<div class="px-2 md:px-4 font-black text-center text-gray-300 rounded-xl
    {{ $allRated ? 'bg-amber-600' : 'bg-gray-700' }}"
    x-cloak>
    <p class="text-xs md:text-base">Rated</p>
    <p class="text-sm md:text-lg">{{ $ratedCount }} / {{ $playerCount }}</p>
</div>