<div {{ $attributes->merge(['class' => 'w-full p-1']) }}>
    <div class="flex items-center justify-center w-full md:gap-x-3">        
        <!-- HomeTeam -->
        @if ($teamsData['home'])
            <div class="flex items-center justify-end w-full">
                <div class="flex items-center justify-end h-full mr-3 space-x-1">
                    <img src="{{ asset($teamsData['home']['img']) }}" class="w-6 h-6 md:w-10 md:h-10">
                </div>
            </div>
        @endif

        <!-- Score -->
        <div class="flex justify-center font-black text-gray-300 rounded
            {{ $fixtureData['winner']
                ? 'bg-green-500'
                : ($fixtureData['winner'] === false ? 'bg-red-600' : 'bg-gray-500') }}">
            <div class="flex px-3 space-x-1.5 md:px-3 w-fit md:text-xl">
                <p>{{ $scoreData['fulltime']['home'] }}</p>
                <p>-</p>
                <p>{{ $scoreData['fulltime']['away'] }}</p>
            </div>
        </div>

        <!-- AwayTeam -->
        @if ($teamsData['away'])
            <div class="flex items-center justify-start w-full">
                <div class="flex items-center justify-end h-full ml-3 space-x-1">                        
                    <img src="{{ asset($teamsData['away']['img']) }}" class="w-6 h-6 md:w-10 md:h-10">
                </div>
            </div>
        @endif
    </div>
</div>