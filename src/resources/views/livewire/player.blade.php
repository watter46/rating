<div id="player" class="hidden w-full text-center" wire:ignore>
    <div class="flex justify-center">
        @if($player['img']['exists'])
            <img src="{{ $player['img']['data'] }}"
                class="w-20 h-20 rounded-full cursor-pointer player"
                wire:click="toDetail({{ $player['id'] }})">
        @endif

        @if(!$player['img']['exists'])
            <img src="{{ $player['img']['data'] }}"
                class="relative w-20 h-20 bg-orange-300 rounded-full cursor-pointer player"
                wire:click="toDetail({{ $player['id'] }})">

            <div class="absolute flex items-center justify-center w-20 h-20">
                <p class="text-3xl font-black text-white">{{ $player['number'] }}</p>
            </div>
        @endif
    </div>

    <p class="font-black text-white whitespace-nowrap">{{ $player['name'] }}</p>

    @vite(['resources/css/player.css'])
</div>