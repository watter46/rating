<div {{ $attributes->merge(['class' => 'w-full p-2']) }}>
    <div class="flex justify-between w-full mb-4 font-black">
        <div class="flex">
            <!-- Date -->
            <p class="pr-2 text-xs text-gray-400 md:text-lg">{{ $fixture['first_half_at'] }}</p>

            <!-- IsRate -->
            @if ($isRate)
                <div class="flex items-center justify-center h-full w-fit"
                    title="Rated">
                    <x-svg.rated-icon-image
                        class="w-3 h-3 md:w-4 md:h-4" />
                </div>
            @endif
        </div>

        <!-- League -->
        <div class="flex justify-end gap-x-2">
            <p class="text-xs text-gray-300 truncate md:text-base">{{ $league['name'] }}</p>
            <p class="text-xs text-gray-300 truncate md:text-base">{{ $league['round'] }}</p>
            <img src="{{ asset($league['img']) }}" class="hidden w-5 h-5 bg-pink-500 rounded-lg object-fit md:block">
        </div>
    </div>
  
    <div class="relative flex items-center justify-center w-full md:gap-x-3">        
        <!-- HomeTeam -->
        @if ($teams['home'])
            <div class="flex items-center justify-end w-full">
                <div class="flex items-center justify-end h-full mr-1 space-x-1">
                    <p class="text-sm font-black text-gray-300 truncate md:text-xl whitespace-nowrap">
                        {{ $teams['home']['name'] }}
                    </p>

                    <img src="{{ asset($teams['home']['img']) }}" class="w-6 h-6 md:w-10 md:h-10">
                </div>
            </div>
        @endif

        <!-- Score -->
        <div class="flex justify-center font-black text-gray-300 rounded
            {{ $fixture['winner']
                ? 'bg-green-500'
                : ($fixture['winner'] === false ? 'bg-red-600' : 'bg-gray-500') }}">
            <div class="flex px-1 space-x-1.5 md:px-3 w-fit md:text-xl">
                <p>{{ $score['fulltime']['home'] }}</p>
                <p>-</p>
                <p>{{ $score['fulltime']['away'] }}</p>
            </div>
        </div>

        <!-- AwayTeam -->
        @if ($teams['away'])
            <div class="flex items-center justify-start w-full">
                <div class="flex items-center justify-end h-full ml-1 space-x-1">                        
                    <img src="{{ asset($teams['away']['img']) }}" class="w-6 h-6 md:w-10 md:h-10">

                    <p class="text-sm font-black text-gray-300 truncate md:text-xl whitespace-nowrap">
                        {{ $teams['away']['name'] }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>