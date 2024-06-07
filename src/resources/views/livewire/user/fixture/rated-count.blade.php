<div class="w-full"
    x-cloak
    x-data="{ isZero: @entangle('isZero') }">
    <label class="text-sm font-black text-gray-400">Rated</label>
    <div class="flex items-center w-full gap-x-3">
        <div class="items-center w-full bg-gray-200 rounded-sm transform skew-x-[-30deg] border border-[rgba(37,255,33,0.42)] dark:bg-gray-700">
            <div class="w-full p-1"
                :class="isZero ? 'bg-gray-700' : 'bg-[#26FF21]'"
                style="width: {{ $ratedPercentage }}%">
            </div>
        </div>

        <label class="text-sm font-black text-gray-400">{{ $ratedPercentage }}%</label>
    </div>
</div>